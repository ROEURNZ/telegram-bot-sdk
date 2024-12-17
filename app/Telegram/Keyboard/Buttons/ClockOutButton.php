<?php

namespace App\Telegram\Keyboard\Buttons;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ClockOutButton
{
    // Method to send a button to share contact
     // Method to send a "Clock In" button
    public function clockOutButton($chatId)
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button([
                    'text' => '🔴 Clock Out 🔴',
                ]),
            ]);

        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Click the button below to clock out.',
                'reply_markup' => $keyboard,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending clock out button: ' . $e->getMessage());
        }
    }
}