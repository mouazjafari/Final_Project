<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::get();
        $total = $orders->count();
        return view('admin.orders', compact('orders', 'total'));
    }

    public function show($id)
    {
        try {
            // ✅ تحميل كل العلاقات المطلوبة
            $order = Order::with([
                'user',
                'address.city',
                'designOrders.size',
                'designOrders.design.images',
                'designOrders.options' // ✅ هون المشكلة - كان معلق
            ])->findOrFail($id);

            // تنسيق البيانات
            $orderData = [
                'id' => $order->id,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'notes' => $order->notes,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'user' => [
                    'name' => $order->user->name ?? 'غير محدد',
                    'email' => $order->user->email ?? 'غير محدد',
                ],
                'address' => [
                    'street' => $order->address->street ?? 'غير محدد',
                    'city' => $order->address->city->getTranslation('name', 'ar') ?? 'غير محدد',
                ],
                'designOrders' => []
            ];

            // معالجة التصاميم
            if ($order->designOrders && $order->designOrders->count() > 0) {
                foreach ($order->designOrders as $designOrder) {

                    // ✅ استخراج الاسم العربي من JSON
                    $designName = 'غير محدد';
                    if ($designOrder->design && $designOrder->design->name) {
                        $nameData = is_string($designOrder->design->name)
                            ? json_decode($designOrder->design->name, true)
                            : $designOrder->design->name;

                        $designName = $nameData['ar'] ?? $nameData['en'] ?? 'غير محدد';
                    }

                    $designData = [
                        'id' => $designOrder->id,
                        'design_name' => $designName,
                        'quantity' => $designOrder->quantity ?? 0,
                        'unit_price' => $designOrder->unit_price ?? 0, // ✅ سعر الوحدة
                        'total_price' => ($designOrder->unit_price ?? 0) * ($designOrder->quantity ?? 0), // ✅ السعر الإجمالي
                        'size' => null,
                        'design_images' => [],
                        'selected_options' => [] // ✅ هون راح نحط الـ options
                    ];

                    // ✅ معالجة المقاس
                    if ($designOrder->size) {
                        $sizeName = is_string($designOrder->size->name)
                            ? json_decode($designOrder->size->name, true)
                            : $designOrder->size->name;

                        $designData['size'] = [
                            'id' => $designOrder->size->id,
                            'name' => is_array($sizeName)
                                ? ($sizeName['ar'] ?? $sizeName['en'] ?? $designOrder->size->name)
                                : $designOrder->size->name
                        ];
                    }

                    // ✅ معالجة الصور
                    if ($designOrder->design && $designOrder->design->images) {
                        foreach ($designOrder->design->images as $image) {
                            $designData['design_images'][] = [
                                'path' => $image->image_path ?? null
                            ];
                        }
                    }

                    // ✅ معالجة الـ Options المختارة
                    if ($designOrder->options && $designOrder->options->count() > 0) {
                        foreach ($designOrder->options as $option) {
                            // فحص إذا الاسم JSON string
                            $optionName = $option->name;
                            if (is_string($optionName)) {
                                $decoded = json_decode($optionName, true);
                                if (is_array($decoded)) {
                                    $optionName = $decoded; // خليه array
                                }
                            }

                            $designData['selected_options'][] = [
                                'id' => $option->id,
                                'name' => $optionName, // هون راح يكون array مثل: ['ar' => 'أحمر', 'en' => 'Red']
                                'type' => $option->type ?? 'غير محدد'
                            ];
                        }
                    }

                    $orderData['designOrders'][] = $designData;
                }
            }

            return response()->json(['order' => $orderData]);
        } catch (\Exception $e) {
            Log::error('Error fetching order details: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => 'حدث خطأ أثناء تحميل بيانات الطلب',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function updateStatus($id, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            $order = Order::with(['user.wallet', 'payments'])->findOrFail($id);
            $oldStatus = $order->status;
            $newStatus = $request->status;

            // إذا كانت الحالة القديمة processing والجديدة cancelled، يتم إرجاع المبلغ للمحفظة
            if ($oldStatus === 'processing' && $newStatus === 'cancelled') {
                $payment = $order->payments()
                    ->where('status', 'completed')
                    ->first();

                if ($payment && $order->user->wallet) {
                    // إرجاع المبلغ للمحفظة
                    $order->user->wallet->deposit([
                        'amount' => $payment->amount,
                        'notes' => 'Refund for cancelled order #' . $order->id . ' (by admin)',
                    ]);

                    // تحديث حالة الدفعة إلى refunded
                    $payment->update(['status' => 'refunded']);

                    Log::info('Order refunded to wallet by admin', [
                        'order_id' => $order->id,
                        'amount' => $payment->amount,
                        'user_id' => $order->user_id,
                    ]);
                }
            }

            $order->status = $newStatus;
            $order->save();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة'
            ], 500);
        }
    }
}
