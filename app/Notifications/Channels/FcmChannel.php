<?php

namespace App\Notifications\Channels;

use App\Services\FirebaseService;
use Illuminate\Notifications\Notification;

class FcmChannel
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$token = $notifiable->fcm_token) {
            return;
        }

        $message = $notification->toFcm($notifiable);

        if (!$message) {
            return;
        }

        $this->firebase->sendToDevice(
            $token,
            $message['notification'] ?? [],
            $message['data'] ?? []
        );
    }
}
