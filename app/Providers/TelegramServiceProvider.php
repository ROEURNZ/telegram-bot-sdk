<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Telegram\Handlers\StartCommandHandler;

class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Telegram::addCommand(StartCommandHandler::class);
    }

    public function boot()
    {
        //
    }
}
