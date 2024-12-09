<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramCommandService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = config('telegram.bots.mybot.token'); // Ensure your bot token is in the config
    }

    public function setCommandMenu()
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/setMyCommands";

        $commands = [
            ['command' => 'start', 'description' => 'Start the bot'],
            ['command' => 'help', 'description' => 'Get help'],
            ['command' => 'decode', 'description' => 'Decode a barcode or QR code'],
            // Add more commands as needed
        ];

        $response = Http::post($url, [
            'commands' => json_encode($commands),
        ]);

        if ($response->successful()) {
            return "Command menu set successfully.";
        }

        return "Failed to set command menu: " . $response->body();
    }
}
