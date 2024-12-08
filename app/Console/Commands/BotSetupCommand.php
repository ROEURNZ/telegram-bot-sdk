<?php

namespace App\Console\Commands;

use Telegram\Bot\Api;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BotSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the Telegram bot webhook';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $telegram = new Api(config('telegram.bots.mybot.token'));
        $webhookUrl = config('telegram.bots.mybot.webhook_url');
        
        $url = sprintf('%s/api/telegram/webhooks/inbound', $webhookUrl);

        try {
            $response = $telegram->setWebhook(['url' => $url]);

            // Log success message to the webhook.log
            Log::channel('webhook')->info('Telegram webhook set successfully', [
                'url' => $url,
                'response' => $response,
            ]);

            $this->info('Webhook set successfully: ' . $url);
            $this->info(json_encode($response));
        } catch (\Exception $e) {
            // Log error message to the webhook.log
            Log::channel('webhook')->error('Failed to set Telegram webhook', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            $this->error('Failed to set webhook: ' . $e->getMessage());
        }
    }
}


