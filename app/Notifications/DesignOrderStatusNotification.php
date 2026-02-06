<?php

namespace App\Notifications;

use App\Models\DesignOrder;
use App\Notifications\Channels\FcmChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DesignOrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DesignOrder $designOrder)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', FcmChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Design Order Status Updated')
            ->line('Your design order #' . $this->designOrder->id . ' status has been updated.')
            ->line('Current Status: ' . $this->designOrder->status)
            ->action('View Design Order', url('/design-orders/' . $this->designOrder->id))
            ->line('Thank you for using our design service!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'design_order_id' => $this->designOrder->id,
            'status' => $this->designOrder->status,
            'message' => 'Your design order #' . $this->designOrder->id . ' status is now ' . $this->designOrder->status . '.',
        ];
    }

    public function toFcm(object $notifiable): array
    {
        return [
            'notification' => [
                'title' => 'Design Order Status Updated',
                'body' => 'Design order #' . $this->designOrder->id . ' is now ' . $this->designOrder->status,
                'sound' => 'default',
            ],
            'data' => [
                'type' => 'design_order_status',
                'design_order_id' => (string) $this->designOrder->id,
                'status' => $this->designOrder->status,
                'action' => 'go_to_design_order_details',
            ],
        ];
    }
}
