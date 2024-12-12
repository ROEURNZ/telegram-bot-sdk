<?php

namespace App\Telegram\Commands\CallbackQueries;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Helpers\EnvDrivenHelper;

class VisitCallbackQuery extends EnvDrivenHelper
{
    public function handleCallbackQuery($callbackQuery)
{
    $chatId = $callbackQuery['message']['chat']['id'];
    $callbackData = $callbackQuery['data'];

    if ($callbackData == 'startvisit') {
        // Start the visit
        return $this->startVisit($chatId);
    }

    if ($callbackData == 'endvisit') {
        // End the visit
        return $this->endVisit($chatId);
    }

    return $this->unknownCommand($chatId);
}

public function startVisit($chatId)
{
    // Logic for starting the visit
    $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN'). '/sendMessage';
    $response = Http::post($url, [
        'chat_id' => $chatId,
        'text' => "Visit has started.",
    ]);

    // Update the visit status in cache
    Cache::put('visit_started_' . $chatId, true, 3600); // Set it as started

    return $response->successful() ? "Visit started." : "Failed to start visit.";
}

public function endVisit($chatId)
{
    // Logic for ending the visit
    $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
    $response = Http::post($url, [
        'chat_id' => $chatId,
        'text' => "Visit has ended.",
    ]);

    // Update the visit status in cache
    Cache::put('visit_started_' . $chatId, false, 3600); // Set it as not started

    return $response->successful() ? "Visit ended." : "Failed to end visit.";
}

public function unknownCommand($chatId)
{
    $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN'). '/sendMessage';
    $response = Http::post($url, [
        'chat_id' => $chatId,
        'text' => "Unknown command.",
    ]);
    return $response->successful() ? "Unknown command." : "Failed to process command.";
}

}
