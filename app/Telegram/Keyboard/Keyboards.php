<?php

namespace App\Telegram\Keyboard;

use Telegram\Bot\Keyboard\Keyboard;
use App\Services\BaseTelegramService;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Functions\Send\TelegramSender;
use App\Telegram\Functions\Send\BaseTelegram;

class Keyboards extends BaseTelegram
{
    protected $telegramSender;

    public function __construct()
    {
        $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
    }

    /**
     * Send the keyboard and wait for user interaction.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @return void
     */
    public function setKeyboards($chat_id)
    {
        $reply_markup = Keyboard::make()
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->row([
                Keyboard::button('🇺🇸 𝗘𝗻𝗴𝗹𝗶𝘀𝗵'),
                Keyboard::button('🇰🇭 𝗞𝗵𝗺𝗲𝗿'),
            ]);

        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Please select a language:',
            'reply_markup' => $reply_markup,
        ]);
    }


    /**
     * Handle user response and remove the keyboard.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @param string $selectedOption The option selected by the user.
     * @return void
     */
    public function handleUserResponse($chat_id, $selectedOption)
    {
        // Respond based on user selection
        $message = "You selected: **$selectedOption**";

        // Send confirmation and remove the keyboard
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown',
            'reply_markup' => Keyboard::remove(),
            // 'reply_markup' => json_encode(['remove_keyboard' => true]),
        ]);
    }
}
