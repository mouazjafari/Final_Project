<?php

namespace App\Notifications;

use App\Models\Design;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DesignCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Design $design)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'design_id' => $this->design->id,
            'design_name' => $this->design->name ?? 'Design #' . $this->design->id,
            'user_name' => $this->design->user->name ?? 'Unknown',
            'title' => 'New Design Created',
            'body' => 'A new design has been created by a user. Tap to review it in the design list',
            'action' => 'go_to_design_list',
            'message' => 'A new design has been created by ' . ($this->design->user->name ?? 'a user'),
        ];
    }
}
