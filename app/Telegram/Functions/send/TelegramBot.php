<?php

namespace App\Telegram\Functions\Send;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

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
        $this->token = env('TELEGRAM_BOT_TOKEN');
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
            "Content-Type" => "application/json",
            "Accept" => "application/json",
        ];
    }

    /**
     * getImageUrl
     *
     * @param  array $photo
     * @return string
     */
    public function getImageUrl(array $photo)
    {
        $image_url = '';

        // Get the last photo's file_id (assuming the last entry is the largest)
        $file_id = $photo[count($photo) - 1]['file_id'];

        // Set the URL to get the file path
        $url = "{$this->api_endpoint}/bot{$this->token}/getFile?file_id={$file_id}";

        // Send the request
        try {
            $response = Http::withHeaders($this->headers)->get($url);
            if ($response->ok()) {
                $result = $response->json();
                if (isset($result['result']['file_path'])) {
                    $file_path = $result['result']['file_path'];
                    $image_url = "{$this->api_endpoint}/file/bot{$this->token}/{$file_path}";
                } else {
                    $result['error'] = 'File path not found in response';
                    Log::error('TelegramBot->getImageUrl->error', ['error' => $result['error']]);
                }
            } else {
                $result['error'] = $response->json()['description'] ?? 'Unknown error';
                Log::error('TelegramBot->getImageUrl->response_error', ['error' => $result['error']]);
            }
        } catch (\Throwable $th) {
            $result['error'] = $th->getMessage();
            Log::error('TelegramBot->getImageUrl->exception', ['error' => $result['error']]);
        }

        Log::info('TelegramBot->getImageUrl->result', ['result' => $result]);
        return $image_url;
    }
}
