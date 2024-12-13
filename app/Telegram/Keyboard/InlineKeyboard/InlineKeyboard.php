<?php

namespace App\Telegram\Keyboard\InlineKeyboard;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class InlineKeyboard
{
    // Method to send inline keyboard for asking contact info
    public function askContactInfo($chatId)
    {
        $inlineKeyboard = Keyboard::make()
            ->inline()
            ->row([
                Keyboard::inlineButton(['text' => '✅ Yes', 'callback_data' => 'yes_contact']),
                Keyboard::inlineButton(['text' => '❌ No', 'callback_data' => 'no_contact']),
            ]);

        try {
            // Send a message with the inline keyboard
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Do you want to share your contact info?',
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending inline keyboard: ' . $e->getMessage());
        }
    }
}
