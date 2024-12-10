<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramCommandService
{
    protected $botToken;

    public function __construct()
    {
        $this->botToken = config('telegram.bots.mybot.token');
    }

    public function setCommandMenu()
    {
        $url = "https://api.telegram.org/bot{$this->botToken}/setMyCommands";

        $commands = array(
            array(
                'command' => 'start',
                'description' => 'Start the bot'
            ),
            array(
                'command' => 'sharecontact',
                'description' => 'Share your contact infomation'
            ),
            array(
                'command' => 'changelanguage',
                'description' => 'Change the system language.'
            ),
            array(
                'command' => 'manage',
                'description' => 'Manage the visits'
            ),
            array(
                'command' => 'help',
                'description' => 'Get help'
            ),
        );

        $response = Http::post($url, [
            'commands' => json_encode($commands),
        ]);

        if ($response->successful()) {
            return "Command menu set successfully.";
        }

        return "Failed to set command menu: " . $response->body();
    }
}
