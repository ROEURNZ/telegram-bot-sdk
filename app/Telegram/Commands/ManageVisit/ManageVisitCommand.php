<?php

namespace App\Telegram\Commands\ManageVisit;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Laravel\Facades\Telegram;

use App\Helpers\EnvDrivenHelper;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class ManageVisitCommand extends EnvDrivenHelper
{

    /**
     * Handle the /manage command.
     */
    public function handleManageCommand($chatId)
    {
        // Check if the visit state is stored in cache for this user
        $visitStarted = Cache::get('visit_started_' . $chatId, false);

        if ($visitStarted) {
            return $this->sendEndVisitOptions($chatId);
        } else {
            return $this->sendStartVisitOptions($chatId);
        }
    }

    /**
     * Send the Start Visit options.
     */
    public function sendStartVisitOptions($chatId)
    {
        $inlineKeyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => '🚶 Start Visit', 'callback_data' => 'startvisit']),
            ]);

        try {
            // Send a message with the inline keyboard
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Do you want to start your visit?',
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending inline keyboard: ' . $e->getMessage());
        }
    }

    /**
     * Send the End Visit options.
     */
    public function sendEndVisitOptions($chatId)
    {
        $inlineKeyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => '☑️ End Visit', 'callback_data' => 'endvisit']),
            ]);

        try {
            // Send a message with the inline keyboard
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Do you want to end visit?',
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending inline keyboard: ' . $e->getMessage());
        }
    }

    /**
     * Handle the callback queries for startvisit and endvisit.
     */
    public function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $callbackData = $callbackQuery['data'];

        if ($callbackData == 'startvisit') {
            return $this->startVisit($chatId);
        }

        if ($callbackData == 'endvisit') {
            return $this->endVisit($chatId);
        }

        return $this->unknownCommand($chatId);
    }

    /**
     * Start the visit.
     */
    public function startVisit($chatId)
    {
        $url = "{$this->api_endpoint}/bot{$this->token}/sendMessage";
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => "Visit has started.",
        ]);

        Cache::put('visit_started_' . $chatId, true, 3600); // Visit started

        return $response->successful() ? "Visit started." : "Failed to start visit.";
    }

    /**
     * End the visit.
     */
    public function endVisit($chatId)
    {
        $url = "{$this->api_endpoint}/bot{$this->token}/sendMessage";
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => "Visit has ended. You can start a new visit.",
        ]);

        // Unset the visit state from cache (remove it completely)
        Cache::forget('visit_started_' . $chatId);  // This will remove the cache entry

        return $response->successful() ? "Visit ended and reset to default." : "Failed to end visit.";
    }



    /**
     * Handle unknown commands.
     */
    public function unknownCommand($chatId)
    {
        $url = "{$this->api_endpoint}/bot{$this->token}/sendMessage";
        $response = Http::post($url, [
            'chat_id' => $chatId,
            'text' => "Unknown command.",
        ]);

        return $response->successful() ? "Unknown command." : "Failed to process command.";
    }
}
