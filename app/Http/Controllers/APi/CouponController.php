<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplyCouponRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\Api\CouponService;
use App\Models\Order;
use Illuminate\Support\Facades\Gate;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * تطبيق كوبون على أوردر
     * POST /api/order/{order}/apply-coupon
     */
    public function apply(ApplyCouponRequest $request, Order $order)
    {
        // Gate::authorize('apply', $order);

        try {
            $result = $this->couponService->applyCouponToOrder(
                $request->code,
                $order
            );

            return $this->success([
                'order' => new OrderResource($result['order']),
                'discount_amount' => $result['discount_amount'],
                'new_total' => $result['new_total'],
            ], $result['message'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    /**
     * إزالة كوبون من أوردر
     * POST /api/order/{order}/remove-coupon
     */
    public function remove(Order $order)
    {
        // Gate::authorize('apply', $order);

        try {
            $result = $this->couponService->removeCouponFromOrder($order);

            return $this->success([
                'order' => new OrderResource($result['order']),
            ], $result['message'], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}
