<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Http\Enum\OrderStatusEnum;
use App\Models\Address;
use App\Models\Design;
use App\Models\Order;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function getMyCurrentOrder()
    {
        $order = Order::where('user_id', Auth::id())->where('status', OrderStatusEnum::Pending)
            ->first();

        return $order;
    }
    public function getMyOrder()
    {
        $order = Order::where('user_id', Auth::id())->get();
        return $order;
    }

    public function add_Design(array $data)
    {
        return DB::transaction(callback: function () use ($data) {
            try {
                $order = Order::where('user_id', Auth::id())
                    ->where('status', OrderStatusEnum::Pending)
                    ->first();
                $address = Address::where('id', $data['address_id'])
                    ->where('user_id', Auth::id())
                    ->first();
                if (is_null($address)) {
                    throw new GeneralException('Address isn\'t yours', 404);
                }
                $isNewOrder = is_null($order);

                if ($isNewOrder) {
                    $order = Order::create([
                        'user_id' => Auth::id(),
                        'address_id' => $data['address_id'],
                        'notes' => $data['notes'] ?? null,
                        'total_price' => 0,
                        'status' => OrderStatusEnum::Pending
                    ]);
                }

                $design = Design::where('id', $data['design_id'])->first();

                if (is_null($design)) {
                    throw new GeneralException('Design not found', 404);
                }

                if ($design->user_id == Auth::id()) {
                    throw new GeneralException('You cannot order your own design', 403);
                }

                if ($design->quantity < $data['quantity']) {
                    throw new GeneralException(
                        "The quantity of the product is $design->quantity",
                        403
                    );
                }

                // ✅ فحص معدّل: السماح بنفس التصميم والمقاس لكن بخيارات مختلفة
                $existingDesignOrders = DB::table('design_order')
                    ->where('order_id', $order->id)
                    ->where('design_id', $data['design_id'])
                    ->where('size_id', $data['size_id'])
                    ->get();

                if ($existingDesignOrders->isNotEmpty()) {
                    // التحقق من الخيارات المطلوب إضافتها
                    $requestedOptions = collect($data['options'])->sort()->values()->all();

                    foreach ($existingDesignOrders as $existingOrder) {
                        // جلب الخيارات المختارة للتصميم الموجود
                        $existingOptions = DB::table('design_option_selected')
                            ->where('design_order_id', $existingOrder->id)
                            ->pluck('design_option_id')
                            ->sort()
                            ->values()
                            ->all();

                        // إذا كانت الخيارات متطابقة تماماً
                        if ($requestedOptions == $existingOptions) {
                            throw new GeneralException(
                                'This design with the same size and options already exists in your order. Please choose different options or update the existing item.',
                                403
                            );
                        }
                    }
                }

                // إضافة التصميم للطلب
                $order->design()->attach($data['design_id'], [
                    'size_id'   => $data['size_id'],
                    'quantity'  => $data['quantity'] ?? 1,
                    'unit_price' => $design->price,
                ]);

                $designOrder = DB::table('design_order')
                    ->where('order_id', $order->id)
                    ->where('design_id', $data['design_id'])
                    ->latest('id')
                    ->first();

                /* =======================
               DESIGN OPTIONS (المعدل)
               ======================= */

                $optionIds = $data['options'];

                // التحقق إنو كل option تابع للـ design
                $selectedOptions = $design->designOptions()
                    ->whereIn('design_options.id', $optionIds)
                    ->get();

                if ($selectedOptions->count() !== count($optionIds)) {
                    throw new GeneralException(
                        'One or more options do not belong to this design',
                        422
                    );
                }

                // التحقق من عدم اختيار أكثر من option من نفس النوع
                $typeCount = $selectedOptions->groupBy('type')->map(function ($group) {
                    return $group->count();
                });

                foreach ($typeCount as $type => $count) {
                    if ($count > 1) {
                        throw new GeneralException(
                            "You can only select one option per type. Multiple options selected for type: {$type}",
                            422
                        );
                    }
                }

                // إدخال الـ options في pivot
                foreach ($optionIds as $optionId) {
                    DB::table('design_option_selected')->insert([
                        'design_order_id'  => $designOrder->id,
                        'design_option_id' => $optionId,
                    ]);
                }

                /* ======================= */

                $order->total_price += $design->price * $data['quantity'];
                $order->save();

                $order->load('user', 'address', 'design', 'designOrders.options');

                // إرسال إشعار إذا كان طلب جديد
                if ($isNewOrder) {
                    // إرسال إشعار لصاحب التصميم
                    $design->user->notify(new OrderCreatedNotification($order));

                    // إرسال إشعار للمشتري (صاحب الطلب)
                    $order->user->notify(new OrderCreatedNotification($order));

                    // إرسال إشعار للمستخدمين اللي عندهم صلاحية عرض الطلبات في الـ web
                    $permission = \Spatie\Permission\Models\Permission::where('name', 'view orders')
                        ->where('guard_name', 'web')
                        ->first();

                    if ($permission) {
                        $usersWithPermission = $permission->users;
                        foreach ($usersWithPermission as $user) {
                            $user->notify(new OrderCreatedNotification($order));
                        }
                    }
                }

                return $order;
            } catch (\Exception $e) {
                Log::error('Error creating design: ' . $e->getMessage());
                throw $e;
            }
        });
    }
    public function update_Design(array $data, int $designOrderId)
    {
        return DB::transaction(function () use ($data, $designOrderId) {
            try {
                // جلب الطلب المعلق للمستخدم
                $order = Order::where('user_id', Auth::id())
                    ->where('status', OrderStatusEnum::Pending)
                    ->first();

                if (is_null($order)) {
                    throw new GeneralException('No pending order found', 404);
                }

                // تحديث العنوان والملاحظات على مستوى الطلب (إذا تم إرسالهم)
                if (isset($data['address_id'])) {
                    $address = Address::where('id', $data['address_id'])
                        ->where('user_id', Auth::id())
                        ->first();

                    if (is_null($address)) {
                        throw new GeneralException('Address isn\'t yours', 404);
                    }

                    $order->address_id = $data['address_id'];
                }

                if (isset($data['notes'])) {
                    $order->notes = $data['notes'];
                }

                // التحقق من design_order المحدد
                $designOrder = DB::table('design_order')
                    ->where('id', $designOrderId)
                    ->where('order_id', $order->id)
                    ->first();

                if (is_null($designOrder)) {
                    throw new GeneralException('Design not found in your order', 404);
                }

                // جلب التصميم
                $design = Design::where('id', $designOrder->design_id)->first();

                if (is_null($design)) {
                    throw new GeneralException('Design not found', 404);
                }

                // التحقق من الكمية المتوفرة
                if (isset($data['quantity']) && $design->quantity < $data['quantity']) {
                    throw new GeneralException(
                        "The quantity of the product is $design->quantity",
                        403
                    );
                }

                // ✅ التحقق المعدل: السماح بنفس التصميم والمقاس لكن بخيارات مختلفة
                $newSizeId = $data['size_id'] ?? $designOrder->size_id;
                $newOptions = $data['options'] ?? null;

                // إذا تم تغيير المقاس أو الخيارات، نتحقق من عدم التكرار
                if (($newSizeId != $designOrder->size_id) || $newOptions !== null) {
                    // جلب التصاميم الموجودة بنفس الـ design_id و size_id
                    $existingDesignOrders = DB::table('design_order')
                        ->where('order_id', $order->id)
                        ->where('design_id', $designOrder->design_id)
                        ->where('size_id', $newSizeId)
                        ->where('id', '!=', $designOrderId)
                        ->get();

                    if ($existingDesignOrders->isNotEmpty()) {
                        // الخيارات الجديدة (إذا لم يتم إرسالها، نستخدم الخيارات الحالية)
                        if ($newOptions === null) {
                            $newOptions = DB::table('design_option_selected')
                                ->where('design_order_id', $designOrderId)
                                ->pluck('design_option_id')
                                ->toArray();
                        }

                        $requestedOptions = collect($newOptions)->sort()->values()->all();

                        foreach ($existingDesignOrders as $existingOrder) {
                            // جلب الخيارات المختارة للتصميم الموجود
                            $existingOptions = DB::table('design_option_selected')
                                ->where('design_order_id', $existingOrder->id)
                                ->pluck('design_option_id')
                                ->sort()
                                ->values()
                                ->all();

                            // إذا كانت الخيارات متطابقة تماماً
                            if ($requestedOptions == $existingOptions) {
                                throw new GeneralException(
                                    'This design with the same size and options already exists in your order. Please choose different options.',
                                    403
                                );
                            }
                        }
                    }
                }

                // حساب السعر القديم لطرحه من الإجمالي
                $oldTotalPrice = $designOrder->unit_price * $designOrder->quantity;

                // تحديث بيانات design_order
                $updateData = [];

                if (isset($data['size_id'])) {
                    $updateData['size_id'] = $data['size_id'];
                }

                if (isset($data['quantity'])) {
                    $updateData['quantity'] = $data['quantity'];
                }

                if (!empty($updateData)) {
                    DB::table('design_order')
                        ->where('id', $designOrderId)
                        ->update($updateData);

                    // إعادة جلب البيانات المحدثة
                    $designOrder = DB::table('design_order')
                        ->where('id', $designOrderId)
                        ->first();
                }

                // تحديث الخيارات (options) إذا تم إرسالها
                if (isset($data['options']) && is_array($data['options'])) {
                    // التحقق من أن كل option تابع للتصميم
                    $validOptionCount = $design->designOptions()
                        ->whereIn('design_options.id', $data['options'])
                        ->count();

                    if ($validOptionCount !== count($data['options'])) {
                        throw new GeneralException(
                            'One or more options do not belong to this design',
                            422
                        );
                    }

                    // حذف الخيارات القديمة
                    DB::table('design_option_selected')
                        ->where('design_order_id', $designOrderId)
                        ->delete();

                    // إضافة الخيارات الجديدة
                    foreach ($data['options'] as $optionId) {
                        DB::table('design_option_selected')->insert([
                            'design_order_id'  => $designOrderId,
                            'design_option_id' => $optionId,
                        ]);
                    }
                }

                // حساب السعر الجديد
                $newTotalPrice = $designOrder->unit_price * $designOrder->quantity;

                // تحديث السعر الإجمالي للطلب
                $order->total_price = $order->total_price - $oldTotalPrice + $newTotalPrice;

                // حفظ التغييرات (العنوان والملاحظات والسعر)
                $order->save();

                // تحميل العلاقات
                $order->load('address', 'design', 'designOrders.options');

                return $order;
            } catch (\Exception $e) {
                Log::error('Error updating design: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function cancelOrder($order)
    {
        return DB::transaction(function () use ($order) {
            $oldStatus = $order->status;

            // التحقق من أن الطلب قابل للإلغاء (pending أو processing فقط)
            if (!in_array($order->status, [OrderStatusEnum::Pending->value, OrderStatusEnum::Processing->value])) {
                throw new GeneralException(
                    'This order cannot be cancelled. Only pending or processing orders can be cancelled.',
                    403
                );
            }

            // إذا كانت الحالة processing، يتم إرجاع المبلغ للمحفظة
            if ($order->status === OrderStatusEnum::Processing->value) {
                // جلب الدفعة المكتملة
                $payment = $order->payments()
                    ->where('status', 'completed')
                    ->first();

                Log::info('Checking refund', [
                    'order_id' => $order->id,
                    'order_status' => $order->status,
                    'payment' => $payment ? $payment->toArray() : null,
                ]);

                if ($payment) {
                    // جلب محفظة المستخدم
                    $wallet = $order->user->wallet;

                    if ($wallet) {
                        // إرجاع المبلغ للمحفظة
                        $wallet->deposit([
                            'amount' => $payment->amount,
                            'notes' => 'Refund for cancelled order #' . $order->id,
                        ]);

                        // تحديث حالة الدفعة إلى refunded
                        $payment->update(['status' => 'refunded']);

                        Log::info('Order refunded to wallet', [
                            'order_id' => $order->id,
                            'amount' => $payment->amount,
                            'user_id' => $order->user_id,
                        ]);
                    } else {
                        Log::warning('User has no wallet', ['user_id' => $order->user_id]);
                    }
                } else {
                    Log::warning('No completed payment found for order', ['order_id' => $order->id]);
                }
            }

            // تحديث حالة الطلب إلى ملغي
            $order->status = OrderStatusEnum::Cancelled;
            $order->save();

            // إرسال إشعار بتحديث حالة الطلب
            $order->user->notify(new OrderStatusUpdatedNotification($order, $oldStatus));

            return $order;
        });
    }
}
