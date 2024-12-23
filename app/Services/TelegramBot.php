<?php

namespace App\Services;

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

        // Set the URL to get the file path: https://api.telegram.org/bot<Your-Bot-token>/getFile?file_id=<Your-file-id>
        $url = "{$this->api_endpoint}/bot{$this->token}/getFile?file_id={$file_id}";

        // Send the request
        try {
            // Send the HTTP GET request to the Telegram API
            $response = Http::withHeaders($this->headers)->get($url);

            // Check if the response is successful
            if ($response->ok()) {
                $result = $response->json();

                // Check if the response contains the expected file_path
                if (isset($result['result']['file_path'])) {
                    $file_path = $result['result']['file_path'];

                    // Construct the image URL: https://api.telegram.org/file/bot<Your-Bot-token>/<Your-file-path>
                    $image_url = "{$this->api_endpoint}/file/bot{$this->token}/{$file_path}";
                } else {
                    // Handle the case where file_path is not available in the response
                    $result['error'] = 'File path not found in response';
                    Log::error('TelegramBot->getImageUrl->error', ['error' => $result['error']]);
                }
            } else {
                // Handle unsuccessful response
                $result['error'] = $response->json()['description'] ?? 'Unknown error';
                Log::error('TelegramBot->getImageUrl->response_error', ['error' => $result['error']]);
            }
        } catch (\Throwable $th) {
            // Catch and log any exceptions
            $result['error'] = $th->getMessage();
            Log::error('TelegramBot->getImageUrl->exception', ['error' => $result['error']]);
        }

        // Log the result for debugging
        Log::info('TelegramBot->getImageUrl->result', ['result' => $result]);

        return $image_url;
    }
}
