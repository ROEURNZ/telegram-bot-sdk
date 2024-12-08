<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Illuminate\Support\Facades\Log;

class UnsetWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:unset-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unset the Telegram bot webhook';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegram = new Api(config('telegram.bots.default.token'));

        try {
            $response = $telegram->removeWebhook();

            // Log success message to the webhook.log
            Log::channel('webhook')->info('Telegram webhook unset successfully', [
                'response' => $response,
            ]);

            $this->info('Webhook unset successfully.');
            $this->info(json_encode($response));
        } catch (\Exception $e) {
            // Log error message to the webhook.log
            Log::channel('webhook')->error('Failed to unset Telegram webhook', [
                'error' => $e->getMessage(),
            ]);

            $this->error('Failed to unset webhook: ' . $e->getMessage());
        }
    }
}
