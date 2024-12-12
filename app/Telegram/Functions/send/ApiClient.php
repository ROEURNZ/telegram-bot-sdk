<?php

namespace App\Telegram\Functions\Send;

use Illuminate\Support\Facades\Log;

// /**
//  * ApiClient
//  */
// class ApiClient
// {
//     protected $token;
//     protected $api_endpoint;
//     protected $headers;

//     public function __construct()
//     {
//         $this->token = env('TELEGRAM_BOT_TOKEN');
//         $this->api_endpoint = env('TELEGRAM_API_ENDPOINT');
//         $this->setHeaders();
//     }

//     protected function setHeaders()
//     {
//         $this->headers = [
//             "Content-Type" => "application/json",
//             "Accept" => "application/json",
//         ];
//     }

//     public function sendMessage($params, $method = 'sendMessage', $log = true, $useCA = false)
//     {
//         if (isset($params['text']) && !isset($params['parse_mode'])) {
//             $params['parse_mode'] = 'HTML';  // Default to HTML
//         }

//         $method = isset($method) ? $method : 'sendMessage';

//         $url = "{$this->api_endpoint}/bot{$this->token}/{$method}";

//         // Initialize cURL session
//         $handle = curl_init($url);
//         curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 60);
//         curl_setopt($handle, CURLOPT_TIMEOUT, 120);
//         curl_setopt($handle, CURLOPT_VERBOSE, true);
//         curl_setopt($handle, CURLOPT_DNS_SERVERS, "8.8.8.8");
//         curl_setopt($handle, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

//         if ($useCA) {
//             curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . "/cacert.pem");
//         }

//         curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($params));

//         // Execute the cURL request
//         $response = curl_exec($handle);

//         if ($response === false) {
//             $errorNumber = curl_errno($handle);
//             $errorMessage = curl_error($handle);
//             curl_close($handle);

//             if (!$useCA) {
//                 sleep(1);
//                 return $this->sendMessage($params, $method, $log, true);
//             }

//             Log::error('ApiClient->sendMessage->CURL Error', [
//                 'errorNumber' => $errorNumber,
//                 'errorMessage' => $errorMessage,
//                 'params' => $params,
//             ]);

//             return ['success' => false, 'error' => $errorMessage];
//         }

//         curl_close($handle);

//         return ['success' => true, 'body' => json_decode($response, true)];
//     }
// }
