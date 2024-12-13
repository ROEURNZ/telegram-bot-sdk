<?php

namespace App\Telegram\Functions\Reply;

use App\Telegram\Functions\Send\BaseTelegram;
use App\Telegram\Functions\Send\TelegramSender;
use Illuminate\Support\Facades\Log;

/**
 * ReplyText
 */
class ReplyMessage extends BaseTelegram
{

    protected $telegramSender;

    public function __construct()
    {
        $this->telegramSender = new TelegramSender($this->token, $this->api_endpoint, $this->headers);
    }


    /**
     * replyMessage
     *
     * @param string $text
     * @param string $chat_id
     * @param int $reply_to_message_id
     * @return array
     */
    public function replyMessage($text, $chat_id, $reply_to_message_id)
    {
        Log::info('replyMessage: Function called', [
            'chat_id' => $chat_id,
            'reply_to_message_id' => $reply_to_message_id,
            'text' => $text,
        ]);

        $params = [
            'chat_id'             => $chat_id,
            'reply_to_message_id' => $reply_to_message_id,
            'text'                => $text,
        ];

        Log::info('replyMessage: Parameters prepared', ['params' => $params]);

        // Call the sendMessage function
        $result = $this->telegramSender->sendRequest($params, 'sendMessage');

        if ($result['success']) {
            Log::info('replyMessage: Message sent successfully', ['result' => $result]);
        } else {
            Log::error('replyMessage: Failed to send message', ['error' => $result['error'] ?? 'Unknown error']);
        }

        return $result;
    }
}
