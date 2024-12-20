<?php

namespace App\Providers;


use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Telegram\TelegramExtendSocialite;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        URL::forceScheme('https');

        Event::listen(
            SocialiteWasCalled::class,
            TelegramExtendSocialite::class . '@handle'
        );
    }

    public function boot(): void
    {
        // Implicitly grant "Super Admin" role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
        if (env('APP_ENV') == 'local') {
            URL::forceScheme('https');
        }
    }
}
