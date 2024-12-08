<?php

namespace App\Providers;

use App\Services\ImageDetectText;
use App\Services\TelegramBot;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        URL::forceScheme('https');

        // create a singleton telegram_bot.
        $this->app->singleton('telegram_bot',function(){
            return new TelegramBot();
        });

        // create a singleton image_detect_text.
        $this->app->singleton('image_detect_text',function(){
            return new ImageDetectText();
        });
    }

    public function boot(): void
    {
        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
