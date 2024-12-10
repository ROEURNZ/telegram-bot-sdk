<?php

namespace App\Telegram\Inc\Functions;
// use App\Telegram\Inc\Functions\SendFunction;

class SendDocument
{

    public function sendDocument(string $document, string $chat_id, string $caption = '', string $parse_mode = 'HTML'): array
    {
        // Prepare the parameters
        $params = [
            'chat_id' => $chat_id,
        ];

        // Validate document URL or file path
        if (filter_var($document, FILTER_VALIDATE_URL)) {
            // If it's a URL, use it as-is
            $params['document'] = $document;
        } elseif (file_exists($document)) {
            // If it's a local file path, create a CURLFile object
            $params['document'] = new \CURLFile(realpath($document));
        } else {
            // If the document is invalid (not a URL or file), handle it appropriately
            return ['success' => false, 'error' => 'Invalid document path or URL'];
        }

        // Add caption if provided
        if (!empty($caption)) {
            $params['caption'] = $caption;
        }

        // Add parse mode if provided
        if (!empty($parse_mode)) {
            $params['parse_mode'] = $parse_mode;
        }

        // Prepare the API URL for sending the document (assuming you have a $url for the API endpoint)
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendDocument';

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
