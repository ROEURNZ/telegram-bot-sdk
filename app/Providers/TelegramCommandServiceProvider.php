<?php

namespace App\Providers;

// From Illuminate Support -> System Provider
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;


// From Commands
use App\Telegram\Commands\StartCommand;
use App\Telegram\Commands\ManageVisit\ManageVisitCommand;

class TelegramCommandServiceProvider extends ServiceProvider
{
    public function register()
    {
        URL::forceScheme('https');

        $this->app->singleton('start_command', fn () => new StartCommand());
        $this->app->singleton('manage_visit', fn () => new ManageVisitCommand());

    }

    public function boot()
    {

        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
