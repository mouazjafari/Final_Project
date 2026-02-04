<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'title' => 'Order Status Updated',
            'body' => 'The status of your order #' . $this->order->id . ' has been updated to ' . $this->order->status . '. Tap to view details',
            'message' => 'Order #' . $this->order->id . ' status changed from ' . $this->oldStatus . ' to ' . $this->order->status,
        ];
    }
}
