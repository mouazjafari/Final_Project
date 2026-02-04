<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CouponCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Coupon $coupon)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Coupon Available!')
            ->line('A new coupon is now available for you!')
            ->line('Coupon Code: ' . $this->coupon->code)
            ->line('Discount: ' . $this->coupon->discount_value . ($this->coupon->discount_type === 'percentage' ? '%' : ' USD'))
            ->line('Valid until: ' . $this->coupon->expiry_date?->format('Y-m-d'))
            ->action('Shop Now', url('/'))
            ->line('Don\'t miss this offer!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'coupon_id' => $this->coupon->id,
            'code' => $this->coupon->code,
            'discount_value' => $this->coupon->discount_value,
            'discount_type' => $this->coupon->discount_type,
            'expiry_date' => $this->coupon->expiry_date,
            'message' => 'New coupon "' . $this->coupon->code . '" is now available!',
        ];
    }
}
