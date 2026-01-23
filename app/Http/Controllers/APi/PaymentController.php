<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Services\Api\PaymentService;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * إنشاء Stripe Checkout Session
     * POST /api/user/payment/create-checkout/{order}
     */
    public function createCheckout(Request $request, Order $order)
    {
        try {
            $result = $this->paymentService->createCheckoutSession(
                $order,
            );

            return $this->success($result, 'Checkout session created successfully', 200);
        } catch (\Exception $e) {
            $statusCode = is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600
                ? (int)$e->getCode()
                : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $statusCode);
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
            $statusCode = is_numeric($e->getCode()) && $e->getCode() >= 100 && $e->getCode() < 600
                ? (int)$e->getCode()
                : 500;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }

    /**
     * عرض تفاصيل الدفعة
     * GET /api/user/payment/{payment}
     */
    public function show(Payment $payment)
    {
        try {
            if ($payment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            return $this->success($payment->load('order'), 'Payment details retrieved successfully', 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }
    }
}
