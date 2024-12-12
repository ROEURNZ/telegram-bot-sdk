<?php

namespace App\Traits;

use Illuminate\Support\Facades\URL;

trait HttpsEnforcer
{
    /**
     * Force HTTPS scheme.
     */
    public function forceHttps()
    {
        URL::forceScheme('https');
    }

    /**
     * Check if the environment is local.
     *
     * @return bool
     */
    public function isLocalEnvironment()
    {
        return env('APP_ENV') === 'local';
    }
}
