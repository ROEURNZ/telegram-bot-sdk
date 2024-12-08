<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ImageDetectText
{
    protected $apiEndpoint;
    protected $headers;

    /**
     * ImageDetectText constructor.
     */
    // public function __construct()
    // {
    //     $this->apiEndpoint = env('AWS_LAMBDA_FUNCTION');
    //     $this->setHeaders();
    // }

    public function __construct()
{
    $this->apiEndpoint = env('AWS_LAMBDA_FUNCTION'); // Keep this as is to load from the .env

    if (is_null($this->apiEndpoint)) {
        // Set a default URL or handle the error
        Log::warning('API endpoint not set, using fallback URL.');
        $this->apiEndpoint = 'https://fallback-url.com';  // Or use null and handle errors accordingly
    }

    $this->setHeaders();
}

    /**
     * Set the necessary headers for the API request.
     */
    protected function setHeaders()
    {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Call AWS Lambda function to detect text from the image.
     *
     * @param string $imageUrl The URL of the image to process.
     * @return array The result of the Lambda function.
     */
    public function lambda($imageUrl)
    {
        $result = [
            'success' => false,
            'body' => [],
            'error' => null,
        ];

        // Ensure that the image URL is not null and is a valid string
        if (empty($imageUrl)) {
            $result['error'] = 'Image URL cannot be empty';
            return $result;
        }

        $params = [
            'image_url' => $imageUrl,
        ];

        try {
            // Send a POST request to the Lambda function endpoint
            $response = Http::withHeaders($this->headers)->post($this->apiEndpoint, $params);

            if ($response->ok()) {
                $result['success'] = true;
                $result['body'] = $response->json();
            } else {
                $result['error'] = $response->body();
            }
        } catch (\Throwable $th) {
            // Log and return the error
            $result['error'] = $th->getMessage();
        }

        Log::info('ImageDetectText->lambda', ['result' => $result]);

        return $result;
    }

    /**
     * Get text from an image by calling the Lambda function.
     *
     * @param string $imageUrl The URL of the image.
     * @return string The parsed text from the image.
     */
    public function getTextFromImage($imageUrl)
    {
        $result = $this->lambda($imageUrl);

        return $this->parseResult($result);
    }

    /**
     * Parse the result from the Lambda function and format the output text.
     *
     * @param array $data The result data from the Lambda function.
     * @return string The formatted text from the image.
     */
    public function parseResult($data)
    {
        $text = "Image Detect Text Bot 🤖:\r\n";
        $index = 1;

        if ($data['success']) {
            if (isset($data['body']['data']) && is_array($data['body']['data'])) {
                foreach ($data['body']['data'] as $line) {
                    if (isset($line['type']) && $line['type'] === 'LINE') {
                        $text .= "{$index}) {$line['text']}\r\n";
                        $index++;
                    }
                }
            } else {
                $text .= "No text detected in the image.\r\n";
            }
        } else {
            $text .= "Error: {$data['error']}\r\n";
        }

        return $text;
    }
}
