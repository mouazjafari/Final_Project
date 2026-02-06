<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';
    protected $description = 'Generate VAPID keys for Web Push notifications';

    public function handle()
    {
        $keys = VAPID::createVapidKeys();

        $this->info('VAPID keys generated successfully!');
        $this->line('');
        $this->line('Add these to your .env file:');
        $this->line('');
        $this->info('VAPID_PUBLIC_KEY=' . $keys['publicKey']);
        $this->info('VAPID_PRIVATE_KEY=' . $keys['privateKey']);
        $this->line('');
        $this->comment('Also add your email:');
        $this->info('VAPID_SUBJECT=mailto:your-email@example.com');

        return 0;
    }
}
