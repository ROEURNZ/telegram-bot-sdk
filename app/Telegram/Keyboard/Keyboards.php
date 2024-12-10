<?php

// namespace App\Telegram\Keyboard;

// use App\Services\TelegramSender;
// use Telegram\Bot\Keyboard\Keyboard;
// use App\Services\BaseTelegramService;
// use Telegram\Bot\Laravel\Facades\Telegram;

// class Keyboards extends BaseTelegramService
// {
//     protected $telegramSender;

//     public function __construct()
//     {
//         $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
//     }

//     /**
//      * Send the keyboard and remove it after user interaction.
//      *
//      * @param int|string $chat_id The chat ID to send the message to.
//      * @return void
//      */
//     public function setKeyboards($chat_id)
//     {
//         // Send a custom keyboard to the user
//         $reply_markup = Keyboard::make()
//             ->setResizeKeyboard(true)
//             ->setOneTimeKeyboard(true)
//             ->row([
//                 Keyboard::button('1'),
//                 Keyboard::button('2'),
//                 Keyboard::button('3'),
//             ])
//             ->row([
//                 Keyboard::button('4'),
//                 Keyboard::button('5'),
//                 Keyboard::button('6'),
//                 Keyboard::button('7'),
//             ])
//             ->row([
//                 // Keyboard::button('7'),
//                 Keyboard::button('8'),
//                 Keyboard::button('9'),
//             ])
//             ->row([
//                 Keyboard::button('0'),
//                 Keyboard::button('11'),
//             ]);

//         $response = Telegram::sendMessage([
//             'chat_id' => $chat_id,
//             'text' => 'Please select a number:',
//             'reply_markup' => $reply_markup
//         ]);

//         // Optionally, handle the response or log it
//         // Log::info('Keyboard sent successfully', ['chat_id' => $chat_id, 'response' => $response]);

//         // After sending the keyboard, remove it by sending an empty message
//         // $this->removeKeyboard($chat_id);
//     }

//     /**
//      * Remove the keyboard by sending a message with no keyboard.
//      *
//      * @param int|string $chat_id The chat ID to send the message to.
//      * @return void
//      */
//     private function removeKeyboard($chat_id)
//     {
//         // Send a message with no keyboard (to remove it)
//         Telegram::sendMessage([
//             'chat_id' => $chat_id,
//             'text' => 'Keyboard removed.',
//             'reply_markup' => json_encode(['remove_keyboard' => true])
//         ]);
//     }
// }



namespace App\Telegram\Keyboard;

use App\Services\TelegramSender;
use Telegram\Bot\Keyboard\Keyboard;
use App\Services\BaseTelegramService;
use Telegram\Bot\Laravel\Facades\Telegram;

class Keyboards extends BaseTelegramService
{
    protected $telegramSender;

    public function __construct()
    {
        $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
    }

    /**
     * Send the keyboard and remove it after user interaction.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @return void
     */
    public function setKeyboards($chat_id)
    {
        // Send a custom keyboard to the user
        $reply_markup = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('1'),
                Keyboard::button('2'),
                Keyboard::button('3'),
            ])
            ->row([
                Keyboard::button('4'),
                Keyboard::button('5'),
                Keyboard::button('6'),
            ])
            ->row([
                Keyboard::button('7'),
                Keyboard::button('8'),
                Keyboard::button('9'),
            ])
            ->row([
                Keyboard::button('0'),
            ]);

        $response = Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Please select a number:',
            'reply_markup' => $reply_markup
        ]);

        // Optionally, handle the response or log it
        // Log::info('Keyboard sent successfully', ['chat_id' => $chat_id, 'response' => $response]);

        // After sending the keyboard, remove it by sending a message with no keyboard.
        $this->removeKeyboard($chat_id);
    }

    /**
     * Remove the keyboard by sending a message with no keyboard using selective removal.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @return void
     */
    private function removeKeyboard($chat_id)
    {
        // Send a message with the keyboard removed using selective option
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Keyboard removed.',
            'reply_markup' => Keyboard::remove(['selective' => false])
        ]);
    }
}
