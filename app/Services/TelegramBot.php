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
     * sendMessage
     *
     * @param  mixed $text
     * @param  mixed $chat_id
     * @param  mixed $reply_to_message_id
     * @return void
     */


    function prepareMessage(
        $keyboard = null,
        $msg = null,
        $photo_url = null,
        $method = null,
        $chat_id = null,
        $keyboard_config = array('resize' => true, 'one_time' => false, 'force_reply' => true),
        $inline_keyboard = false,
        $inline_keyboard_config = null,
        $edit_msg_id = null,
        $multiple_inline = false,
        $document = null,
        $video = null
    ) {
        global $userId, $ezzeTeamsModel;

        $keyboard_settings = [
            'keyboard' => $keyboard,
            'resize_keyboard' => (isset($keyboard_config['resize'])) ? $keyboard_config['resize'] : true,
            'one_time_keyboard' => (isset($keyboard_config['one_time'])) ? $keyboard_config['one_time'] : false,
            'force_reply_keyboard' => (isset($keyboard_config['force_reply'])) ? $keyboard_config['force_reply'] : true,
        ];

        if ($inline_keyboard) {
            $keyboard_settings = ["inline_keyboard" => [$inline_keyboard_config]];
            if ($multiple_inline) {
                $keyboard_settings = ["inline_keyboard" => $inline_keyboard_config];
            }
        }

        $text = str_replace(array('_'), chr(10), $msg);
        $text = str_replace(array('###'), array('_'), $text);

        $params = [
            'chat_id' => $userId,
            'parse_mode' => 'HTML',
        ];

        if ($method == 'editMessageText') {
            $params['message_id'] = $edit_msg_id;
        }

        if (isset($msg) && !isset($photo_url)) {
            $params['text'] = $text;
        }

        if (isset($photo_url)) {
            $params['caption'] = $text;
            $params['photo'] = $photo_url;
        }

        if (isset($keyboard) || $inline_keyboard) {
            $params['reply_markup'] = json_encode($keyboard_settings);
        }

        if (isset($document)) {
            $params['caption'] = $text;
            $params['document'] = $document;
        }

        if (isset($video)) {
            $params['caption'] = $text;
            $params['video'] = $video;
        }
        $params['disable_message_delete'] = true;

        if (!is_null($chat_id) && isset($chat_id) && is_array($chat_id) && count($chat_id) > 0) {
            foreach ($chat_id as $id) {
                $params['chat_id'] = $id;
                sendMessage($params, $method);
            }
        } else {
            sendMessage($params, $method);
        }
    }



    function deleteMessage($msg_id, $user_id)
    {
        $params = [
            'chat_id' => $user_id,
            'message_id' => $msg_id,
        ];
        sendMessage($params, 'deleteMessage');
    }


    // public function replyMessage($text, $chat_id, $reply_to_message_id)
    // {
    //     $params = [
    //         'chat_id'             => $chat_id,
    //         'reply_to_message_id' => $reply_to_message_id,
    //         'text'                => $text,
    //     ];

    //     // Call the sendMessage function
    //     $result = $this->sendMessage($params, 'sendMessage');

    //     return $result;
    // }



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



    public function replyMessage($text, $chat_id, $reply_to_message_id)
    {
        Log::info('replyMessage: Function called', [
            'chat_id' => $chat_id,
            'reply_to_message_id' => $reply_to_message_id,
            'text' => $text,
        ]);

        $params = [
            'chat_id'             => $chat_id,
            'reply_to_message_id' => $reply_to_message_id,
            'text'                => $text,
        ];

        Log::info('replyMessage: Parameters prepared', ['params' => $params]);

        // Call the sendMessage function
        $result = $this->sendMessage($params, 'sendMessage');

        if ($result['success']) {
            Log::info('replyMessage: Message sent successfully', ['result' => $result]);
        } else {
            Log::error('replyMessage: Failed to send message', ['error' => $result['error'] ?? 'Unknown error']);
        }

        return $result;
    }




    public function sendMessage($params, $method = null, $log = true, $useCA = false)
    {

        if (isset($params['text'])) {
            if (!isset($params['parse_mode'])) {
                $params['parse_mode'] = 'HTML';  // Default to HTML
            }
        }
        // 'sendMessage',
        $method = isset($method) ? $method : 'sendMessage';
        // Prepare the URL for the API request
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
            curl_setopt($handle, CURLOPT_CAINFO, __DIR__ . "/../../../cacert/cacert.pem");
        }

        // Set the cURL options
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
                return $this->sendMessage($params, $method, $log, true);
            }

            Log::error('TelegramBot->sendMessage->CURL Error', [
                'errorNumber' => $errorNumber,
                'errorMessage' => $errorMessage,
                'params' => $params,
            ]);

            return ['success' => false, 'error' => $errorMessage];
        }

        curl_close($handle);

        return ['success' => true, 'body' => json_decode($response, true)];
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
