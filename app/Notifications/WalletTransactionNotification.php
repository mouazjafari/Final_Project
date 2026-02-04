<?php

namespace App\Notifications;

use App\Models\WalletTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletTransactionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public WalletTransaction $transaction)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCredit = $this->transaction->type === 'credit';
        
        return (new MailMessage)
            ->subject('Wallet Transaction Notification')
            ->line('A new transaction has been made to your wallet.')
            ->line('Type: ' . ucfirst($this->transaction->type))
            ->line('Amount: $' . $this->transaction->amount)
            ->line('Description: ' . ($this->transaction->description ?? 'N/A'))
            ->line('Current Balance: $' . $this->transaction->wallet->balance)
            ->action('View Wallet', url('/wallet'))
            ->line('Thank you!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'type' => $this->transaction->type,
            'amount' => $this->transaction->amount,
            'description' => $this->transaction->description,
            'balance' => $this->transaction->wallet->balance,
            'message' => ucfirst($this->transaction->type) . ' of $' . $this->transaction->amount . ' to your wallet.',
        ];
    }
}
