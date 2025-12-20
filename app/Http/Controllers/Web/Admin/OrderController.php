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
            $order = Order::with([
                'user',
                'address',
                'designOrders.size',
                'designOrders.design.images',
                // 'designOrders.options'
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
                    'city' => $order->address->city ?? 'غير محدد',
                ],
                // 'options' => [
                //     'name' => $order->designOrders->options->name ?? 'غير محدد',
                //     'type' => $order->designOrders->options->type ?? 'غير محدد',
                // ],
                'designOrders' => []
            ];
            dd($orderData);

            // معالجة التصاميم
            if ($order->designOrders) {
                foreach ($order->designOrders as $designOrder) {
                    // استخراج الاسم العربي من JSON
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
                        'size' => null,
                        'design_images' => []
                    ];

                    // معالجة المقاس
                    if ($designOrder->size) {
                        $designData['size'] = [
                            'id' => $designOrder->size->id,
                            'name' => $designOrder->size->name
                        ];
                    }

                    // معالجة الصور
                    if ($designOrder->design && $designOrder->design->images) {
                        foreach ($designOrder->design->images as $image) {
                            $designData['design_images'][] = [
                                'path' => $image->path
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
                'line' => $e->getLine()
            ], 500);
        }
    }
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled'
            ]);

            $order = Order::findOrFail($id);
            $order->status = $request->status;
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
