<?php


namespace App\Providers;

// From Illuminate Support -> System Provider
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

// From Functions 
use App\Telegram\Functions\send\sendPhoto;
use App\Telegram\Functions\send\SendVideo;
use App\Telegram\Functions\send\SendContact;
use App\Telegram\Functions\send\SendMessage;
use App\Telegram\Functions\Reply\ReplyMessage;



class TelegramFunctionServiceProvider extends ServiceProvider
{
    public function register()
    {
        URL::forceScheme('https');

        $this->app->singleton('sendmessage',  fn () => new SendMessage());
        $this->app->singleton('sendcontact',  fn () => new SendContact());
        $this->app->singleton('sendbuttonclockin',  fn () => new SendContact());
        $this->app->singleton('replymessage', fn () => new ReplyMessage());
        $this->app->singleton('sendphoto',    fn () => new sendPhoto());
        $this->app->singleton('sendvideo',    fn () => new SendVideo());

    }

    public function boot()
    {

        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
