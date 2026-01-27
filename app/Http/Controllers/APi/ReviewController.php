<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\updateReviewRequest;
use App\Http\Services\Api\ReviewService;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    protected $reviewservice;
    public function __construct(ReviewService $reviewService)
    {
        $this->reviewservice = $reviewService;
    }
    public function store(StoreReviewRequest $request)
    {
        $order = Order::findOrFail($request->order_id);
        Gate::authorize('create', [Review::class, $order]);
        $review = $this->reviewservice->createReview($request->validated());
        return $this->success($review,  'Review created successfully', 200);
    }
    public function update(Review $review, updateReviewRequest $request)
    {
        Gate::authorize('update', $review);
        $review = $this->reviewservice->updateReview($review, $request->validated());
        return $this->success($review,  'Review updated successfully', 200);
    }
    public function delete(Review $review)
    {
        Gate::authorize('delete', $review);
        $review = $this->reviewservice->deleteReview($review);
        return $this->success($review,  'Review deleted successfully', 200);
    }
}
