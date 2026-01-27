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
        DB::beginTransaction();
        try {
            $order = Order::findOrFail($data['order_id']);
            $review = Review::where('order_id', $order->id)
                ->where('user_id', Auth::id())
                ->first();

            if ($review) {
                throw new GeneralException('You have already reviewed this order.',400);
            }
            
            $data['user_id'] = $order->user_id;

            $review = Review::create($data);
            DB::commit();
            return $review;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function updateReview(Review $review, array $data)
    {
        DB::beginTransaction();
        try {
            $review->update($data);
            DB::commit();
            return $review;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
    public function deleteReview(Review $review)
    {
        $review->delete();
        return true;
    }
}
