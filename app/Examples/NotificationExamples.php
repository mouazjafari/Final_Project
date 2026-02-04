<?php

/**
 * NOTIFICATION USAGE EXAMPLES
 *
 * هذا الملف يحتوي على أمثلة على كيفية استخدام نظام الإشعارات في المشروع
 * This file contains examples of how to use the notification system in the project
 */

namespace App\Examples;

use App\Models\User;
use App\Models\Order;
use App\Models\Payment;
use App\Models\DesignOrder;
use App\Models\Coupon;
use App\Models\WalletTransaction;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\OrderStatusUpdatedNotification;
use App\Notifications\PaymentProcessedNotification;
use App\Notifications\DesignOrderStatusNotification;
use App\Notifications\CouponCreatedNotification;
use App\Notifications\WalletTransactionNotification;

class NotificationExamples
{
    /**
     * Example 1: Send notification when order is created
     * مثال 1: إرسال إشعار عند إنشاء طلب جديد
     */
    public function sendOrderCreatedNotification()
    {
        $user = User::find(1);
        $order = Order::find(1);

        // إرسال الإشعار
        $user->notify(new OrderCreatedNotification($order));
    }

    /**
     * Example 2: Send notification when order status is updated
     * مثال 2: إرسال إشعار عند تحديث حالة الطلب
     */
    public function sendOrderStatusUpdatedNotification()
    {
        $user = User::find(1);
        $order = Order::find(1);
        $oldStatus = 'pending';

        // إرسال الإشعار
        $user->notify(new OrderStatusUpdatedNotification($order, $oldStatus));
    }

    /**
     * Example 3: Send notification when payment is processed
     * مثال 3: إرسال إشعار عند معالجة الدفعة
     */
    public function sendPaymentProcessedNotification()
    {
        $user = User::find(1);
        $payment = Payment::find(1);

        // إرسال الإشعار
        $user->notify(new PaymentProcessedNotification($payment));
    }

    /**
     * Example 4: Send notification when design order status changes
     * مثال 4: إرسال إشعار عند تغيير حالة طلب التصميم
     */
    public function sendDesignOrderStatusNotification()
    {
        $user = User::find(1);
        $designOrder = DesignOrder::find(1);

        // إرسال الإشعار
        $user->notify(new DesignOrderStatusNotification($designOrder));
    }

    /**
     * Example 5: Send notification for new coupon
     * مثال 5: إرسال إشعار عن كوبون جديد
     */
    public function sendCouponCreatedNotification()
    {
        $users = User::all(); // يمكنك تخصيص المستخدمين
        $coupon = Coupon::find(1);

        // إرسال الإشعار لجميع المستخدمين
        foreach ($users as $user) {
            $user->notify(new CouponCreatedNotification($coupon));
        }
    }

    /**
     * Example 6: Send notification for wallet transaction
     * مثال 6: إرسال إشعار عند إجراء معاملة في المحفظة
     */
    public function sendWalletTransactionNotification()
    {
        $user = User::find(1);
        $transaction = WalletTransaction::find(1);

        // إرسال الإشعار
        $user->notify(new WalletTransactionNotification($transaction));
    }

    /**
     * Example 7: Send notification to multiple users
     * مثال 7: إرسال إشعار لعدة مستخدمين
     */
    public function sendToMultipleUsers()
    {
        $users = User::whereIn('id', [1, 2, 3])->get();
        $order = Order::find(1);

        // إرسال الإشعار لعدة مستخدمين
        foreach ($users as $user) {
            $user->notify(new OrderCreatedNotification($order));
        }
    }

    /**
     * Example 8: Queue notification (send later)
     * مثال 8: وضع الإشعار في قائمة الانتظار (إرسال لاحقاً)
     *
     * Note: All our notification classes implement ShouldQueue
     * ملاحظة: جميع فئات الإشعارات تطبق ShouldQueue
     */
    public function queueNotification()
    {
        $user = User::find(1);
        $order = Order::find(1);

        // هذا سيتم إضافته تلقائياً لقائمة الانتظار
        $user->notify(new OrderCreatedNotification($order));
    }

    /**
     * Example 9: Send notification via specific channel
     * مثال 9: إرسال الإشعار عبر قناة محددة
     */
    public function sendViaSpecificChannel()
    {
        $user = User::find(1);
        $order = Order::find(1);

        // إرسال عبر قاعدة البيانات فقط
        $user->notify((new OrderCreatedNotification($order)));
    }

    /**
     * HOW TO USE IN YOUR CONTROLLERS:
     * كيفية الاستخدام في الـ Controllers الخاص بك:
     *
     * في OrderController عند إنشاء طلب جديد:
     *
     * public function create(Request $request) {
     *     $order = Order::create($request->all());
     *
     *     // إرسال إشعار للمستخدم
     *     $request->user()->notify(new OrderCreatedNotification($order));
     *
     *     return response()->json(['order' => $order]);
     * }
     *
     * في OrderController عند تحديث حالة الطلب:
     *
     * public function updateStatus(Request $request, Order $order) {
     *     $oldStatus = $order->status;
     *     $order->update(['status' => $request->status]);
     *
     *     // إرسال إشعار
     *     $order->user->notify(new OrderStatusUpdatedNotification($order, $oldStatus));
     *
     *     return response()->json(['order' => $order]);
     * }
     *
     * في PaymentController عند معالجة الدفعة:
     *
     * public function processPayment(Request $request, Order $order) {
     *     $payment = Payment::create([...]);
     *
     *     // إرسال إشعار
     *     $order->user->notify(new PaymentProcessedNotification($payment));
     *
     *     return response()->json(['payment' => $payment]);
     * }
     */
}
