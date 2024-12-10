<?php

namespace App\Services;



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



    public function sendText($text, $chat_id, $parse_mode = 'HTML')
    {
        // Prepare the parameters
        $params = [
            'chat_id'   => $chat_id,
            'text'      => $text,
        ];

        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        // Call the sendMessage function and return the result
        return $this->sendMessage($params);
    }

}
