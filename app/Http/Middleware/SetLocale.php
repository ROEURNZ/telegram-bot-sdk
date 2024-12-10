<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{

    public function handle(Request $request, Closure $next): Response
    {
        // Option 1: Set locale based on the user's cookie
        
        // $locale = $request->cookie('app_locale', config('app.locale'));
        // App::setLocale($locale);

        // Option 2: Set locale based on the authenticated user's preference
        $locale = Auth::check() ? Auth::user()->locale : config('app.locale');
        App::setLocale($locale);

        return $next($request);
    }
}
