<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Models\Coupon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponService
{
    /**
     * تطبيق الكوبون على الأوردر
     */
    public function applyCouponToOrder(string $code, Order $order)
    {
        return DB::transaction(function () use ($code, $order) {
            try {
                $userId = Auth::id();

                // 1. التحقق من ملكية الأوردر
                if ($order->user_id !== $userId) {
                    throw new GeneralException('لا يمكنك تطبيق كوبون على طلب ليس لك', 403);
                }

                // 2. التحقق من حالة الأوردر
                if ($order->status !== 'pending') {
                    throw new GeneralException('لا يمكن تطبيق كوبون على طلب تم معالجته', 400);
                }

                // 3. التحقق من عدم وجود كوبون مطبق مسبقاً
                if ($order->coupon_id) {
                    throw new GeneralException('تم تطبيق كوبون على هذا الطلب مسبقاً', 400);
                }

                // 4. البحث عن الكوبون
                $coupon = Coupon::where('code', $code)->first();

                if (!$coupon) {
                    throw new GeneralException('الكوبون غير موجود', 404);
                }

                // 5. فحص صلاحية الكوبون
                if (!$coupon->isValid()) {
                    throw new GeneralException('الكوبون غير صالح أو منتهي الصلاحية', 400);
                }

                // 6. فحص: هل يمكن للمستخدم استخدامه؟
                if (!$coupon->canBeUsedBy($userId)) {
                    throw new GeneralException('لا يمكنك استخدام هذا الكوبون', 403);
                }

                // 7. فحص: هل سعر الأوردر كافي؟ (للكوبونات الثابتة)
                if (!$coupon->isOrderTotalSufficient($order->total_price)) {
                    throw new GeneralException(
                        "سعر الطلب يجب أن يكون {$coupon->amount} على الأقل لاستخدام هذا الكوبون",
                        400
                    );
                }

                // 8. حساب الخصم
                $discountAmount = $coupon->calculateDiscount($order->total_price);

                // 9. حساب السعر الجديد
                $newTotal = $order->total_price - $discountAmount;

                // 10. تحديث الأوردر
                $order->update([
                    'coupon_id' => $coupon->id,
                    'discount_amount' => $discountAmount,
                    'total_price' => $newTotal,
                ]);

                // 11. تسجيل استخدام الكوبون
                $coupon->recordUsage($userId, $order->id, $discountAmount);

                // 12. تحميل العلاقات
                $order->load(['coupon', 'designOrders.design', 'address']);

                return [
                    'success' => true,
                    'order' => $order,
                    'discount_amount' => $discountAmount,
                    'new_total' => $newTotal,
                    'message' => "تم تطبيق الكوبون بنجاح! تم خصم {$discountAmount} من طلبك",
                ];

            } catch (GeneralException $e) {
                throw $e;
            } catch (\Exception $e) {
                Log::error('Error applying coupon: ' . $e->getMessage());
                throw new GeneralException('حدث خطأ أثناء تطبيق الكوبون', 500);
            }
        });
    }

    /**
     * إزالة الكوبون من الأوردر
     */
    public function removeCouponFromOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            try {
                $userId = Auth::id();

                if ($order->user_id !== $userId) {
                    throw new GeneralException('لا يمكنك تعديل طلب ليس لك', 403);
                }

                if (!$order->coupon_id) {
                    throw new GeneralException('لا يوجد كوبون مطبق على هذا الطلب', 400);
                }

                if ($order->status !== 'pending') {
                    throw new GeneralException('لا يمكن إزالة الكوبون من طلب تم معالجته', 400);
                }

                // استرجاع السعر الأصلي
                $originalPrice = $order->total_price + $order->discount_amount;

                // حذف سجل الاستخدام
                $order->coupon->usages()
                    ->where('order_id', $order->id)
                    ->where('user_id', $userId)
                    ->delete();

                // تقليل عداد الاستخدام
                $order->coupon->decrement('used_count');

                // تحديث الأوردر
                $order->update([
                    'coupon_id' => null,
                    'discount_amount' => 0,
                    'total_price' => $originalPrice,
                ]);

                return [
                    'success' => true,
                    'order' => $order->load(['designOrders.design', 'address']),
                    'message' => 'تم إزالة الكوبون بنجاح',
                ];

            } catch (GeneralException $e) {
                throw $e;
            } catch (\Exception $e) {
                Log::error('Error removing coupon: ' . $e->getMessage());
                throw new GeneralException('حدث خطأ أثناء إزالة الكوبون', 500);
            }
        });
    }
}
