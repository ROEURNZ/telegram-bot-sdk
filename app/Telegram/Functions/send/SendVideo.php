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



    public function sendVideo(string $video, string $chat_id, string $caption = '', string $parse_mode = 'HTML'): array
    {
        // Prepare the parameters
        $params = [
            'chat_id' => $chat_id,
        ];

        // Validate video URL or file path
        if (filter_var($video, FILTER_VALIDATE_URL)) {
            $params['video'] = $video;
        } elseif (file_exists($video)) {
            $params['video'] = new \CURLFile(realpath($video));
        } else {
            return ['success' => false, 'error' => 'Invalid video path or URL'];
        }

        // Add caption if provided
        if (!empty($caption)) {
            $params['caption'] = $caption;
        }

        // Add parse mode if provided
        if (!empty($parse_mode)) {
            $params['parse_mode'] = $parse_mode;
        }

        // API endpoint for sending video
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendVideo';

        // Initialize cURL
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . '/cacert.pem'); // Path to CA certificate

        // Execute the request
        $response = curl_exec($handle);

        // Handle cURL errors
        if (curl_errno($handle)) {
            $error = curl_error($handle);
            curl_close($handle);
            return ['success' => false, 'error' => "cURL Error: {$error}"];
        }

        curl_close($handle);

        // Decode and return the response
        $decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['ok']) && $decodedResponse['ok']) {
            return ['success' => true, 'response' => $decodedResponse];
        }

        return ['success' => false, 'error' => $decodedResponse['description'] ?? 'Unknown error'];
    }
}
