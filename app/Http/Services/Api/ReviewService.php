<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    public function createReview(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                $order = Order::findOrFail($data['order_id']);

                $existingReview = Review::where('order_id', $order->id)
                    ->where('user_id', Auth::id())
                    ->first();

                if ($existingReview) {
                    throw new GeneralException('You have already reviewed this order.', 400);
                }

                $data['user_id'] = Auth::id();

                $review = Review::create($data);

                return $review;
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    public function updateReview(Review $review, array $data)
    {
        return DB::transaction(function () use ($review, $data) {
            try {
                $review->update($data);
                return $review;
            } catch (\Exception $e) {
                throw $e;
            }
        });
    }

    public function deleteReview(Review $review)
    {
        return DB::transaction(function () use ($review) {
            $review->delete();
            return true;
        });
    }
}
