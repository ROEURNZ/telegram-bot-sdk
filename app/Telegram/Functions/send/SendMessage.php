<?php

namespace App\Telegram\Functions\send;

use App\Telegram\Functions\Send\BaseTelegram;
use Illuminate\Support\Facades\Log;

class SendMessage extends BaseTelegram
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


    public function sendMessages($text, $chat_id, $parse_mode = 'HTML')
    {
        Log::info('SendText: Function called', [
            'chat_id' => $chat_id,
            'text'    => $text,
        ]);

        // Prepare the parameters for the message
        $params = [
            'chat_id' => $chat_id,
            'text'    => $text,
        ];

        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        Log::info('SendText: Parameters prepared', ['params' => $params]);

        // Call the sendRequest function with 'sendMessage' as the method
        $result = $this->telegramSender->sendRequest($params, 'sendMessage');

        if ($result['success']) {
            Log::info('SendText: Message sent successfully', ['result' => $result]);
        } else {
            Log::error('SendText: Failed to send message', ['error' => $result['error'] ?? 'Unknown error']);
        }

        return $result;
    }
}
