<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Services\Api\PaymentService;
use App\Models\Order;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * إنشاء Payment Intent للدفع بالبطاقة
     * POST /api/user/payment/create-intent/{order}
     */
    public function createPaymentIntent(Order $order)
    {
        try {
            $result = $this->paymentService->createPaymentIntent($order);

            return $this->success($result, 'Payment intent created successfully', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * تأكيد الدفع بعد نجاح العملية
     * POST /api/user/payment/confirm
     */
    public function confirmPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $result = $this->paymentService->confirmPayment($request->payment_intent_id);

            return $this->success([
                'payment' => $result['payment'],
                'order' => new OrderResource($result['order']),
            ], 'Payment confirmed successfully', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * الدفع من المحفظة
     * POST /api/user/payment/wallet/{order}
     */
    public function payWithWallet(Order $order)
    {
        try {
            $result = $this->paymentService->payWithWallet($order);

            return $this->success([
                'payment' => $result['payment'],
                'order' => new OrderResource($result['order']),
                'new_wallet_balance' => $result['new_wallet_balance'],
            ], 'Payment successful with wallet', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * عرض تفاصيل الدفعة
     * GET /api/user/payment/{payment_id}
     */
    public function show($paymentId)
    {
        try {
            $payment = \App\Models\Payment::with(['order', 'user'])
                ->where('id', $paymentId)
                ->where('user_id', FacadesAuth::id())
                ->firstOrFail();

            return $this->success($payment, 'Payment details retrieved successfully', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }
    }
}
