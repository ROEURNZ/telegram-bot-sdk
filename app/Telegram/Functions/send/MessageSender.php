<?php

namespace App\Telegram\Functions\Send;

// use Illuminate\Support\Facades\Log;
use App\Telegram\Functions\Send\ApiClient;

/**
 * MessageSender
 */
class MessageSender
{
    protected $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function sendText($text, $chat_id, $parse_mode = 'HTML')
    {
        $params = [
            'chat_id' => $chat_id,
            'text' => $text,
        ];

        if ($parse_mode) {
            $params['parse_mode'] = $parse_mode;
        }

        return $this->apiClient->sendMessage($params);
    }

    public function sendVideo(string $video, string $chat_id, string $caption = '', string $parse_mode = 'HTML')
    {
        $params = [
            'chat_id' => $chat_id,
        ];

        if (filter_var($video, FILTER_VALIDATE_URL)) {
            $params['video'] = $video;
        } elseif (file_exists($video)) {
            $params['video'] = new \CURLFile(realpath($video));
        } else {
            return ['success' => false, 'error' => 'Invalid video path or URL'];
        }

        if (!empty($caption)) {
            $params['caption'] = $caption;
        }

        if (!empty($parse_mode)) {
            $params['parse_mode'] = $parse_mode;
        }

        return $this->apiClient->sendMessage($params);
    }

    public function sendPhoto(string $photo, string $chat_id, string $caption = '', string $parse_mode = 'HTML')
    {
        $params = [
            'chat_id' => $chat_id,
        ];

        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            $params['photo'] = $photo;
        } elseif (file_exists($photo)) {
            $params['photo'] = new \CURLFile(realpath($photo));
        } else {
            return ['success' => false, 'error' => 'Invalid photo path or URL'];
        }

        if (!empty($caption)) {
            $params['caption'] = $caption;
        }

        if (!empty($parse_mode)) {
            $params['parse_mode'] = $parse_mode;
        }

        return $this->apiClient->sendMessage($params);
    }
}
