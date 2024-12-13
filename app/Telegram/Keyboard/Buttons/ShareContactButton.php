<?php

namespace App\Telegram\Keyboard\Buttons;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ShareContactButton
{
    // Method to send a button to share contact
    public function shareContactButton($chatId)
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button([
                    'text' => 'Share Contact',
                    'request_contact' => true,
                ]),
            ]);

        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Please share your contact information.',
                'reply_markup' => $keyboard,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending contact button: ' . $e->getMessage());
        }
    }
}
