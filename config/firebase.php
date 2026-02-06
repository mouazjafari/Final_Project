<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file is used to set up Firebase Cloud Messaging
    | for push notifications.
    |
    */

    'server_key' => env('FIREBASE_SERVER_KEY', ''),

    'api_url' => 'https://fcm.googleapis.com/fcm/send',

    /*
    |--------------------------------------------------------------------------
    | Default Notification Settings
    |--------------------------------------------------------------------------
    */

    'default_notification' => [
        'sound' => 'default',
        'badge' => 1,
        'priority' => 'high',
    ],
];
