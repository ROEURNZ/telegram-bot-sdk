<?php

namespace App\Providers;

use App\Services\SendText;
use App\Services\ReplyText;
use App\Services\SendPhoto;
use App\Services\SendVideo;
use App\Services\TelegramBot;
use App\Services\ImageDetectText;
use App\Services\LanguageService;
use Illuminate\Support\Facades\URL;
use App\Telegram\Keyboard\Keyboards;
use Illuminate\Support\ServiceProvider;
use App\Services\TelegramCommandService;


class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {

        URL::forceScheme('https');

        // create a singleton.
        $this->app->singleton('telegram_bot', function () {
            return new TelegramBot();
        });



        $this->app->singleton('send_text', function () {
            return new SendText();
        });


        $this->app->singleton('reply_text', function () {
            return new ReplyText();
        });

        $this->app->singleton('send_photo', function () {
            return new SendPhoto();
        });

        $this->app->singleton('send_video', function () {
            return new SendVideo();
        });


        $this->app->singleton('image_detect_text', function () {
            return new ImageDetectText();
        });


        $this->app->singleton('system_command', function () {
            return new TelegramCommandService();
        });

        $this->app->singleton('system_buttons', function () {
            return new Keyboards();
        });
    }

    public function boot()
    {

        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
