<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment ' . ucfirst($this->payment->status))
            ->line('Your payment has been ' . $this->payment->status . '.')
            ->line('Amount: $' . $this->payment->amount)
            ->line('Payment Method: ' . $this->payment->payment_method)
            ->action('View Payment', url('/payments/' . $this->payment->id))
            ->line('Thank you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'amount' => $this->payment->amount,
            'status' => $this->payment->status,
            'payment_method' => $this->payment->payment_method,
            'message' => 'Your payment of $' . $this->payment->amount . ' has been ' . $this->payment->status . '.',
        ];
    }
}
