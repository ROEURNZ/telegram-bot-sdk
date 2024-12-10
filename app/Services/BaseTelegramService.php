<?php

namespace App\Services;

/**
 * BaseTelegramService
 */
class BaseTelegramService
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
}
