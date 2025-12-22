<?php

namespace App\Http\Services\Api;

use App\Exceptions\GeneralException;
use App\Http\Enum\OrderStatusEnum;
use App\Models\Address;
use App\Models\Design;
use App\Models\Order;
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
                if (is_null($order)) {
                    $order = Order::create([
                        'user_id' => Auth::id(),
                        'address_id' => $data['address_id'],
                        'notes' => $data['notes'],
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

                $existingDesign = $order->design()
                    ->where('design_id', $data['design_id'])
                    ->wherePivot('size_id', $data['size_id'])
                    ->first();

                if ($existingDesign) {
                    throw new GeneralException(
                        'This design is already exists in your order',
                        403
                    );
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
                $validOptionCount = $design->designOptions()
                    ->whereIn('design_options.id', $optionIds)
                    ->count();

                if ($validOptionCount !== count($optionIds)) {
                    throw new GeneralException(
                        'One or more options do not belong to this design',
                        422
                    );
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

                $order->load('address', 'design', 'designOrders.options');
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

                // التحقق من تكرار نفس التصميم بنفس المقاس (إذا تم تغيير المقاس)
                if (isset($data['size_id']) && $data['size_id'] != $designOrder->size_id) {
                    $existingDesign = DB::table('design_order')
                        ->where('order_id', $order->id)
                        ->where('design_id', $designOrder->design_id)
                        ->where('size_id', $data['size_id'])
                        ->where('id', '!=', $designOrderId)
                        ->exists();

                    if ($existingDesign) {
                        throw new GeneralException(
                            'This design with the same size already exists in your order',
                            403
                        );
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
                $order->load('address', 'design','designOrders.options');

                return $order;
            } catch (\Exception $e) {
                Log::error('Error updating design: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function cancelOrder($order)
    {
        $order->status = OrderStatusEnum::Cancelled;
        $order->save();
        return $order;
    }
}
