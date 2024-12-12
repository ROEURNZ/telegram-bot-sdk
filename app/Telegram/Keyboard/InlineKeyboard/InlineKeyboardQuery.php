<?php

namespace App\Telegram\Keyboard\InlineKeyboard;

use App\Services\TelegramSender;
use Telegram\Bot\Keyboard\Keyboard;
use App\Services\BaseTelegramService;
use Telegram\Bot\Laravel\Facades\Telegram;

class InlineKeyboardQuery extends BaseTelegramService
{
    protected $telegramSender;

    public function __construct()
    {
        // Initialize the TelegramSender service with the necessary parameters.
        $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
    }

    /**
     * Prepare the inline keyboard and send it to the user.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @return void
     */
    public function sendInlineKeyboard($chat_id)
    {
        $keyboard = $this->createInlineKeyboard();

        $this->sendMessage($chat_id, 'Do you want to send your contact?', $keyboard);
    }

    /**
     * Create the inline keyboard.
     *
     * @return Keyboard
     */
    private function createInlineKeyboard()
    {
        return Keyboard::make()
            ->inline()
            ->row([
                Keyboard::button('Yes'),
                Keyboard::button('No'),
            ]);
    }

    /**
     * Send a message to the user with the provided keyboard.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @param string $text The message text.
     * @param Keyboard $keyboard The keyboard to attach.
     * @return void
     */
    private function sendMessage($chat_id, $text, $keyboard)
    {
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => $keyboard,
        ]);
    }

    /**
     * Handle the user's response and provide feedback.
     *
     * @param int|string $chat_id The chat ID to send the message to.
     * @param string $selectedOption The option selected by the user.
     * @return void
     */
    public function handleResponse($chat_id, $selectedOption)
    {
        $message = $this->generateResponseMessage($selectedOption);

        $keyboard = $this->removeKeyboard(); // Remove the inline keyboard

        $this->sendMessage($chat_id, $message, $keyboard);
    }

    /**
     * Generate a formatted response message based on user selection.
     *
     * @param string $selectedOption The option selected by the user.
     * @return string
     */
    private function generateResponseMessage($selectedOption)
    {
        return "**$selectedOption**";
    }

    /**
     * Return a keyboard with no buttons (removes the inline keyboard).
     *
     * @return Keyboard
     */
    private function removeKeyboard()
    {
        return Keyboard::remove();
    }
}
