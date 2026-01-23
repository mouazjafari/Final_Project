<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * إنشاء Stripe Checkout Session
     */
    public function createCheckoutSession(Order $order)
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

            $user = Auth::user();

            // إنشاء Stripe Checkout Session
            $session = Session::create([
                'customer_email' => $user->email,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => "Order #{$order->id}",
                            'description' => "Payment for order #{$order->id}",
                        ],
                        'unit_amount' => (int)($order->total_price * 100), // بالسنت
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('stripe.success'),
                'cancel_url' => route('stripe.cancel'),
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                ],
            ]);

            // حفظ بيانات الدفع في قاعدة البيانات
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'payment_method' => 'card',
                'stripe_session_id' => $session->id,
                'amount' => $order->total_price,
                'status' => 'pending',
                'currency' => 'usd',
                'metadata' => json_encode([
                    'session_url' => $session->url,
                ]),
            ]);

            return [
                'payment_id' => $payment->id,
                'session_id' => $session->id,
                'checkout_url' => $session->url, // 🔥 الرابط للدفع
                'amount' => $order->total_price,
                'currency' => 'usd',
            ];
        } catch (\Exception $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * معالجة Webhook من Stripe
     */
    public function handleWebhook(array $payload, string $signature)
    {
        return DB::transaction(function () use ($payload, $signature) {
            try {
                $webhookSecret = config('services.stripe.webhook_secret');

                // التحقق من صحة Webhook
                $event = Webhook::constructEvent(
                    json_encode($payload),
                    $signature,
                    $webhookSecret
                );

                // معالجة الحدث
                if ($event->type === 'checkout.session.completed') {
                    $session = $event->data->object;

                    // جلب معلومات الطلب من metadata
                    $orderId = $session->metadata->order_id;
                    $userId = $session->metadata->user_id;

                    // جلب الدفعة
                    $payment = Payment::where('stripe_session_id', $session->id)
                        ->where('user_id', $userId)
                        ->where('order_id', $orderId)
                        ->firstOrFail();

                    // تحديث حالة الدفعة
                    $payment->update([
                        'status' => 'completed',
                        'stripe_payment_intent_id' => $session->payment_intent,
                        'paid_at' => now(),
                    ]);

                    // تحديث حالة الطلب
                    $order = $payment->order;
                    $order->update(['status' => 'processing']);

                    // تقليل الكمية من التصاميم
                    foreach ($order->designOrders as $designOrder) {
                        $design = $designOrder->design;
                        $design->decrement('quantity', $designOrder->quantity);
                    }

                    Log::info("Payment completed for Order #{$orderId}");
                }

                return ['status' => 'success'];
            } catch (\Exception $e) {
                Log::error('Webhook Error: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * الدفع من المحفظة (موجود مسبقاً)
     */
    public function payWithWallet(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                if ($order->user_id !== Auth::id()) {
                    throw new GeneralException('Unauthorized access to this order', 403);
                }

                if ($order->status !== 'pending') {
                    throw new GeneralException('This order cannot be paid', 400);
                }

                $user = Auth::user();
                $wallet = $user->wallet;

                if (!$wallet) {
                    throw new GeneralException('Wallet not found', 404);
                }

                if ($wallet->balance < $order->total_price) {
                    throw new GeneralException('Insufficient wallet balance', 400);
                }

                $wallet->withdraw([
                    'amount' => $order->total_price,
                    'notes' => "Payment for order #{$order->id}",
                ]);

                $payment = Payment::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'payment_method' => 'wallet',
                    'amount' => $order->total_price,
                    'status' => 'completed',
                    'currency' => 'ILS',
                    'paid_at' => now(),
                ]);

                $order->update(['status' => 'processing']);

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
