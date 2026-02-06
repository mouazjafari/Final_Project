<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'total_price' => $this->order->total_price,
            'status' => $this->order->status,
            'title' => 'New Order Created',
            'body' => 'A new order has been placed for your design. Tap to view the order details.',
            'action' => 'go_to_order_list',
            'message' => 'A new order #' . $this->order->id . ' has been placed.',
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => 'New Order Created',
                'body' => 'Order #' . $this->order->id . ' - Total: $' . $this->order->total_price,
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'order_created',
                'order_id' => (string) $this->order->id,
                'action' => 'go_to_order_list',
            ],
        ];
    }
}
