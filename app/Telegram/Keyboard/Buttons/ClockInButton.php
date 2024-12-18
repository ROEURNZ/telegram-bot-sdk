<?php

namespace App\Telegram\Keyboard\Buttons;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ClockInButton
{
    // Method to send a button to share contact
     // Method to send a "Clock In" button
    public function clockInButton($chatId)
    {
        $keyboard = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button([
                    'text' => '🟢 Clock In 🟢',
                ]),
            ]);

        try {
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => 'Click the button below to clock in.',
                'reply_markup' => $keyboard,
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending clock in button: ' . $e->getMessage());
        }
    }


    public function getClockInStatus($currentTime)
    {
        $workStartTime = '09:00'; // Example start time
        return $currentTime > $workStartTime ? 'LATE' : 'ON TIME';
    }
}
