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

    public function handleLocation($update)
    {
        // Get the location data from the update
        $location = $update->getMessage()->getLocation();
        $latitude = $location->getLatitude();
        $longitude = $location->getLongitude();

        // You can save the location in the database or take some action here
        Log::info('User shared location: Latitude - ' . $latitude . ', Longitude - ' . $longitude);

        // Optionally, you can send a confirmation message to the user
        $chatId = $update->getMessage()->getChat()->getId();
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Thank you! You have successfully clocked in.',
        ]);
    }
}
