<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Telegram\Commands\Menu\UserCommandMenu;
use App\Helpers\EnvDrivenHelper;
class TelegramCommandService extends EnvDrivenHelper
{

    /**
     * Set the Telegram bot's command menu.
     */
    public function setCommandMenu()
    {
        $commandMenu = new UserCommandMenu();
        $url = "{$this->api_endpoint}/bot{$this->token}/setMyCommands";
        $commands = $commandMenu->commands;
        $response = Http::post($url, [
            'commands' => json_encode($commands),
        ]);

        if ($response->successful()) {
            return "Command menu set successfully.";
        }

        return "Failed to set command menu: " . $response->body();
    }

}
