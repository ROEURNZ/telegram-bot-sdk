<?php

namespace App\Telegram\Functions\Send;

use App\Telegram\Functions\Send\BaseTelegram;

class SendContact extends BaseTelegram
{

    protected $telegramSender;

    public function __construct()
    {
        $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
    }

    /**
     * sendText
     *
     * @param string $text The message text
     * @param int|string $chat_id The chat ID to send the message to
     * @param string $parse_mode The parse mode (default: 'HTML')
     * @return array The result of the sendRequest call
     */

    public function sendContact($chatId, $markup, $token, $replyMarkup, $parse_mode = 'HTML')
    {

        $params = [
            'chat_id' => $chatId,
            'text' => $markup,
            'reply_markup' => $replyMarkup,
        ];

        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        // Send the message
        $result = $this->telegramSender->sendRequest($params, 'sendMessage');

        return $result;
    }
    public function sendButtonClockIn($chatId, $markup, $token, $replyMarkup, $parse_mode = 'HTML')
    {

        $params = [
            'chat_id' => $chatId,
            'text' => $markup,
            'reply_markup' => $replyMarkup,
        ];

        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        // Send the message
        $result = $this->telegramSender->sendRequest($params, 'sendMessage');

        return $result;
    }
}
