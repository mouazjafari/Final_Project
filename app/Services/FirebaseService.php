<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = storage_path('app/firebase/firebase_credentials.json');

            if (!file_exists($credentialsPath)) {
                Log::warning('Firebase credentials file not found at: ' . $credentialsPath);
                return;
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to single device
     */
    public function sendToDevice(string $token, array $notification, array $data = []): bool
    {
        if (!$this->messaging) {
            Log::warning('Firebase messaging not initialized');
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification(Notification::create(
                    $notification['title'] ?? 'Notification',
                    $notification['body'] ?? ''
                ))
                ->withData($data);

            $this->messaging->send($message);

            Log::info('Firebase notification sent successfully', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $notification['title'] ?? '',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase notification failed', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);

            return false;
        }
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToDevices(array $tokens, array $notification, array $data = []): bool
    {
        if (!$this->messaging || empty($tokens)) {
            return false;
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create(
                    $notification['title'] ?? 'Notification',
                    $notification['body'] ?? ''
                ))
                ->withData($data);

            $result = $this->messaging->sendMulticast($message, $tokens);

            Log::info('Firebase multicast sent', [
                'success' => $result->successes()->count(),
                'failures' => $result->failures()->count(),
            ]);

            return $result->successes()->count() > 0;
        } catch (\Exception $e) {
            Log::error('Firebase multicast failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send notification to a topic
     */
    public function sendToTopic(string $topic, array $notification, array $data = []): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification(Notification::create(
                    $notification['title'] ?? 'Notification',
                    $notification['body'] ?? ''
                ))
                ->withData($data);

            $this->messaging->send($message);

            Log::info('Firebase topic notification sent', [
                'topic' => $topic,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase topic notification failed', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
