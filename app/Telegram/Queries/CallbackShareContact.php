<?php

// namespace App\Services;
namespace App\Telegram\Queries;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class CallbackShareContact
{
    public function handleCallback($request)
    {
        $callbackQuery = $request->callback_query;

        if ($callbackQuery) {
            $data       = $callbackQuery['data'];
            $chatId     = $callbackQuery['message']['chat']['id'];
            $messageId  = $callbackQuery['message']['message_id'];

            try {
                // Ensure that the callback has not been handled already
                if ($this->hasHandledCallback($chatId, $messageId)) {
                    return response()->json(['status' => 'Callback already handled']);
                }

                $this->markCallbackAsHandled($chatId, $messageId);

                $messageText = '';
                if ($data === 'yes_contact') {
                    app('shareContactButton')->shareContactButton($chatId);
                } elseif ($data === 'no_contact') {
                    $messageText = 'You skipped sharing your contact info.';
                }

                Telegram::editMessageText([
                    'chat_id' => $chatId,
                    'message_id' => $messageId,
                    'text' => $messageText,
                    'reply_markup' => json_encode(['inline_keyboard' => []]),
                ]);

                // Acknowledge the callback query to remove the loading spinner
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $callbackQuery['id'],
                ]);

            } catch (\Exception $e) {
                Log::error('Error handling callback query: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to handle callback'], 500);
            }
        }

        return response()->json(['error' => 'No callback query found'], 400);
    }

    private function hasHandledCallback($chatId, $messageId)
    {
        return cache()->has("callback_handled_{$chatId}_{$messageId}");
    }

    private function markCallbackAsHandled($chatId, $messageId)
    {
        cache()->put("callback_handled_{$chatId}_{$messageId}", true, now()->addMinutes(5));
    }
}
