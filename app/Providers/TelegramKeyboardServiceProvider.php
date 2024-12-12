<?php

namespace App\Providers;

// From Illuminate Support -> System Provider
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

// From Keyboards
use App\Telegram\Keyboard\Keyboards;
use App\Telegram\Keyboard\Buttons\ShareContactButton;
use App\Telegram\Keyboard\InlineKeyboard\InlineKeyboard;



class TelegramKeyboardServiceProvider extends ServiceProvider
{
    public function register()
    {
        URL::forceScheme('https');

        $this->app->singleton('inline_keyboard', fn () => new InlineKeyboard());
        $this->app->singleton('btn-contact',     fn () => new ShareContactButton());
        $this->app->singleton('system_buttons',  fn () => new Keyboards());
    }

    public function boot()
    {

        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
