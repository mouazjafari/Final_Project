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

    public function createCheckout(Order $order)
    {
        $result = $this->paymentService->createCheckoutSession($order);
        return $this->success($result, 'Checkout session created successfully', 200);
    }
    public function payWithWallet(Order $order)
    {
        $result = $this->paymentService->payWithWallet($order);

        return $this->success([
            'payment' => $result['payment'],
            'order' => new OrderResource($result['order']),
            'new_wallet_balance' => $result['new_wallet_balance'],
        ], 'Payment successful with wallet', 200);
    }

    public function show(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        return $this->success($payment->load('order'), 'Payment details retrieved successfully', 200);
    }
}
