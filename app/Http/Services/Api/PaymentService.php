<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * إنشاء Payment Intent لبدء عملية الدفع
     */
    public function createPaymentIntent(Order $order, string $paymentMethod = 'card')
    {
        try {
            // التحقق من ملكية الطلب
            if ($order->user_id !== Auth::id()) {
                throw new GeneralException('Unauthorized access to this order', 403);
            }

            // التحقق من حالة الطلب
            if ($order->status !== 'pending') {
                throw new GeneralException('This order cannot be paid', 400);
            }

            // التحقق من عدم وجود دفعة مكتملة مسبقاً
            $existingPayment = Payment::where('order_id', $order->id)
                ->where('status', 'completed')
                ->first();

            if ($existingPayment) {
                throw new GeneralException('This order is already paid', 400);
            }

            // إنشاء Payment Intent في Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($order->total_price * 100), // تحويل للـ Agorot (فلوس صغيرة)
                'currency' => 'usd', // الشيكل الإسرائيلي
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                ],
            ]);

            // حفظ بيانات الدفع في قاعدة البيانات
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'payment_method' => $paymentMethod,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'amount' => $order->total_price,
                'status' => 'pending',
                'currency' => 'usd',
                'metadata' => json_encode([
                    'payment_intent_client_secret' => $paymentIntent->client_secret,
                ]),
            ]);

            return [
                'payment_id' => $payment->id,
                'client_secret' => $paymentIntent->client_secret,
                'amount' => $order->total_price,
                'currency' => 'usd',
            ];

        } catch (CardException $e) {
            Log::error('Stripe Card Error: ' . $e->getMessage());
            throw new GeneralException('Payment failed: ' . $e->getMessage(), 400);
        } catch (\Exception $e) {
            Log::error('Payment Intent Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * تأكيد الدفع بعد نجاح العملية
     */
    public function confirmPayment(string $paymentIntentId)
    {
        return DB::transaction(function () use ($paymentIntentId) {
            try {
                // جلب Payment Intent من Stripe
                $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

                // التحقق من نجاح الدفع
                if ($paymentIntent->status !== 'succeeded') {
                    throw new GeneralException('Payment was not successful', 400);
                }

                // جلب الدفعة من قاعدة البيانات
                $payment = Payment::where('stripe_payment_intent_id', $paymentIntentId)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

                // تحديث حالة الدفعة
                $payment->markAsCompleted($paymentIntent->charges->data[0]->id ?? null);

                // تحديث حالة الطلب
                $order = $payment->order;
                $order->update([
                    'status' => 'completed', // أو completed حسب منطق التطبيق
                ]);

                // تقليل الكمية من التصاميم
                foreach ($order->designOrders as $designOrder) {
                    $design = $designOrder->design;
                    $design->decrement('quantity', $designOrder->quantity);
                }

                return [
                    'success' => true,
                    'payment' => $payment,
                    'order' => $order->load('designOrders.design'),
                ];

            } catch (\Exception $e) {
                Log::error('Confirm Payment Error: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * الدفع من المحفظة
     */
    public function payWithWallet(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                // التحقق من ملكية الطلب
                if ($order->user_id !== Auth::id()) {
                    throw new GeneralException('Unauthorized access to this order', 403);
                }

                // التحقق من حالة الطلب
                if ($order->status !== 'pending') {
                    throw new GeneralException('This order cannot be paid', 400);
                }

                // جلب محفظة المستخدم
                $user = Auth::user();
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new GeneralException('Wallet not found', 404);
                }

                // التحقق من الرصيد الكافي
                if ($wallet->balance < $order->total_price) {
                    throw new GeneralException('Insufficient wallet balance', 400);
                }

                // سحب المبلغ من المحفظة
                $wallet->withdraw([
                    'amount' => $order->total_price,
                    'notes' => "Payment for order #{$order->id}",
                ]);

                // إنشاء سجل الدفع
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'payment_method' => 'wallet',
                    'amount' => $order->total_price,
                    'status' => 'completed',
                    'currency' => 'ILS',
                    'paid_at' => now(),
                ]);

                // تحديث حالة الطلب
                $order->update([
                    'status' => 'processing',
                ]);

                // تقليل الكمية من التصاميم
                foreach ($order->designOrders as $designOrder) {
                    $design = $designOrder->design;
                    $design->decrement('quantity', $designOrder->quantity);
                }

                return [
                    'success' => true,
                    'payment' => $payment,
                    'order' => $order->load('designOrders.design'),
                    'new_wallet_balance' => $wallet->fresh()->balance,
                ];

            } catch (\Exception $e) {
                Log::error('Wallet Payment Error: ' . $e->getMessage());
                throw $e;
            }
        });
    }
}
