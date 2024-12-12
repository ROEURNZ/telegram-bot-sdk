<?php

namespace App\Telegram\Functions\Send;

use Illuminate\Support\Facades\Log;
use App\Telegram\Functions\Send\BaseTelegram;

/**
 * TelegramSender
 */
class TelegramSender extends BaseTelegram
{

    /**
     * sendRequest
     *
     * @param array $params Parameters to send with the request
     * @param string|null $method Telegram API method (defaults to 'sendMessage')
     * @param bool $log Whether to log the request
     * @param bool $useCA Whether to use CA for SSL verification
     * @return array Response from the Telegram API
     */
    public function sendRequest($params, $method = null, $log = true, $useCA = false)
    {
        // Set default parse_mode to HTML if 'text' exists and 'parse_mode' is not set
        if (isset($params['text']) && !isset($params['parse_mode'])) {
            $params['parse_mode'] = 'HTML';
        }

        $method = $method ?? 'sendMessage';

        $url = "{$this->api_endpoint}/bot{$this->token}/{$method}";

        // Initialize cURL session
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($handle, CURLOPT_TIMEOUT, 120);
        curl_setopt($handle, CURLOPT_VERBOSE, true);
        curl_setopt($handle, CURLOPT_DNS_SERVERS, "8.8.8.8");
        curl_setopt($handle, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

        if ($useCA) {
            curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . '/../../../Services/cacert.pem');
        }

        // Set the cURL options for POST fields
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));

        // Execute the cURL request
        $response = curl_exec($handle);

        // Error handling for cURL execution
        if ($response === false) {
            $errorNumber = curl_errno($handle);
            $errorMessage = curl_error($handle);
            curl_close($handle);

            if (!$useCA) {
                sleep(1);
                return $this->sendRequest($params, $method, $log, true);
            }

            Log::error('TelegramSender->sendRequest->CURL Error', [
                'errorNumber' => $errorNumber,
                'errorMessage' => $errorMessage,
                'params' => $params,
                'method' => $method,
            ]);

            return ['success' => false, 'error' => $errorMessage];
        }

        curl_close($handle);

        $decodedResponse = json_decode($response, true);

        if ($log) {
            Log::info('TelegramSender->sendRequest->Response', [
                'method' => $method,
                'params' => $params,
                'response' => $decodedResponse,
            ]);
        }

        return ['success' => true, 'body' => $decodedResponse];
    }
}
