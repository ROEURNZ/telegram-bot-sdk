<?php

namespace App\Telegram\Handlers;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommandHandler extends Command
{
    protected $name = 'start'; // The command name (e.g., /start)
    protected $description = 'Start command'; // Command description

    public function handle()
    {
        // Get the incoming message from the user
        $text = $this->getMessage()->getText();

        // Send a message to the user with the command options
        $this->replyWithMessage([
            'text' => "Welcome to the bot! Here are your options:",
            'reply_markup' => Keyboard::make([
                'keyboard' => [
                    ['Option 1'],
                    ['Option 2'],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
            ])
        ]);
    }
}

