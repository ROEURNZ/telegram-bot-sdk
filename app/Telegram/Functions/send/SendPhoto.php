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



    public function sendPhoto(string $photo, string $chat_id, string $caption = '', string $parse_mode = 'HTML'): array
    {
        // Prepare the parameters
        $params = [
            'chat_id' => $chat_id,
        ];

        // Validate photo URL or file path
        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            // If it's a URL, use it as-is
            $params['photo'] = $photo;
        } elseif (file_exists($photo)) {
            // If it's a local file path, create a CURLFile object
            $params['photo'] = new \CURLFile(realpath($photo));
        } else {
            // If the photo is invalid (not a URL or file), handle it appropriately
            return ['success' => false, 'error' => 'Invalid photo path or URL'];
        }

        // Add caption if provided
        if (!empty($caption)) {
            $params['caption'] = $caption;
        }

        // Add parse mode if provided
        if (!empty($parse_mode)) {
            $params['parse_mode'] = $parse_mode;
        }

        // Prepare the API URL for sending the photo (assuming you have a $url for the API endpoint)
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendPhoto';

        // Initialize cURL
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $params);
        curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");

        // Execute the request and capture the response
        $response = curl_exec($handle);

        // Handle cURL errors
        if (curl_errno($handle)) {
            $error = curl_error($handle);
            curl_close($handle);
            return ['success' => false, 'error' => "cURL Error: {$error}"];
        }

        // Close the cURL handle
        curl_close($handle);

        // Decode and return the response
        $decodedResponse = json_decode($response, true);

        if (isset($decodedResponse['ok']) && $decodedResponse['ok']) {
            return ['success' => true, 'response' => $decodedResponse];
        }

        return ['success' => false, 'error' => $decodedResponse['description'] ?? 'Unknown error'];
    }



}
