<?php

namespace App\Telegram\Queries;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Keyboard\Buttons\ShareContactButton;
use App\Telegram\Keyboard\InlineKeyboard\InlineKeyboard;


class InlineCallbackQuery
{
    protected $inlineKeyboard;
    protected $contactButton;

    public function __construct()
    {
        $this->inlineKeyboard = new InlineKeyboard();
        $this->contactButton = new ShareContactButton();
    }

    // Handle callback queries for the Yes/No response
    public function handleCallbackQuery()
    {
        $update = Telegram::getWebhookUpdates();

        // Check if there's a callback query in the update
        $callbackQuery = $update->getCallbackQuery();
        if ($callbackQuery) {
            $data = $callbackQuery->getData();
            $chatId = $callbackQuery->getMessage()->getChat()->getId();

            try {
                if ($data === 'yes_contact') {
                    // If "Yes" is clicked, ask for contact info
                    $this->contactButton->shareContactButton($chatId);
                } elseif ($data === 'no_contact') {
                    // If "No" is clicked, send a skip message
                    Telegram::sendMessage([
                        'chat_id' => $chatId,
                        'text' => 'You skipped sharing your contact.',
                    ]);
                }

                // Acknowledge the callback query
                Telegram::answerCallbackQuery([
                    'callback_query_id' => $callbackQuery->getId(),
                ]);
            } catch (\Exception $e) {
                Log::error('Error handling callback query: ' . $e->getMessage());
            }
        }
    }
}
