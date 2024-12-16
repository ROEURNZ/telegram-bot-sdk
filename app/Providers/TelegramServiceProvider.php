<?php

namespace App\Providers;

// From Illuminate Support -> System Provider
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

// From Services

use App\Services\TelegramCommandService;


class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {
        URL::forceScheme('https');
        // create a singleton.
        
        $this->app->singleton('usercommandmenu', function () {
            return new TelegramCommandService();
        });
    }

    public function boot()
    {
        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
