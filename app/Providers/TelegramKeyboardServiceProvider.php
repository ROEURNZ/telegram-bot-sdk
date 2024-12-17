<?php

namespace App\Providers;

// From Illuminate Support -> System Provider

use App\Telegram\Keyboard\Buttons\ClockInButton;
use App\Telegram\Keyboard\Buttons\ClockOutButton;
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

        $this->app->singleton('requestShareContact', fn () => new InlineKeyboard());
        $this->app->singleton('shareContactButton',     fn () => new ShareContactButton());
        $this->app->singleton('buttonSelectLanguage',  fn () => new Keyboards());
        $this->app->singleton('buttonClockIn',  fn () => new ClockInButton());
        $this->app->singleton('buttonClockOut',  fn () => new ClockOutButton());
    }

    public function boot()
    {

        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
