<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * TelegramBot
 */
class TelegramBot
{
    protected $token;
    protected $api_endpoint;
    protected $headers;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->token        = env('TELEGRAM_BOT_TOKEN');
        $this->api_endpoint = env('TELEGRAM_API_ENDPOINT');
        $this->setHeaders();
    }

    /**
     * setHeaders
     *
     * @return void
     */
    protected function setHeaders()
    {

        $this->headers = [
            "Content-Type"  => "application/json",
            "Accept"        => "application/json",
        ];
    }


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
        $result = $this->sendMessage($params, 'sendMessage');

        if ($result['success']) {
            Log::info('replyMessage: Message sent successfully', ['result' => $result]);
        } else {
            Log::error('replyMessage: Failed to send message', ['error' => $result['error'] ?? 'Unknown error']);
        }

        return $result;
    }
}
