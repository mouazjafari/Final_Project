<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Services\Api\OrderService;
use App\Models\DesignOrder;
use App\Models\Order;
use App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderservice)
    {
        $this->orderService = $orderservice;
    }
    public function show()
    {
        $order = $this->orderService->getMyCurrentOrder();
        Gate::authorize('viewAny', $order);
        return $this->success(new OrderResource($order), 'Order returned successfully !');
    }
    public function index()
    {
        Gate::authorize('view', Order::class);
        $orders = $this->orderService->getMyOrder();
        return $this->success(OrderResource::collection($orders), 'Orders returned successfully !');
    }
    public function create(OrderRequest $request)
    {
        Gate::authorize('create', Order::class);
        $order = $this->orderService->add_Design($request->validated());
        return $this->success(new OrderResource($order), 'Order returned successfully !');
    }
    public function cancel(Order $order)
    {
        Gate::authorize('cancel', $order);
        $order = $this->orderService->cancelOrder($order);
        return $this->success(new OrderResource($order), 'Order returned successfully !');
    }
    public function update(DesignOrder $designOrder, UpdateOrderRequest $request)
    {
        $order = $this->orderService->update_Design($request->validated(), $designOrder->id);
        Gate::authorize('update', $order);
        return $this->success(new OrderResource($order), 'Order returned successfully !');
    }
}
