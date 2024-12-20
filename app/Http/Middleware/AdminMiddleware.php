<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // if (Auth::check() && Auth::user()->is_admin) {
        //     return $next($request);
        // }

        // // Optionally, redirect non-admin users to a different page, or return an error response
        // return redirect('/home')->withErrors('You do not have admin access.');

        if (Auth::check()) {
            /** @var App\Models\User */
            $user = Auth::user();
            if ($user->hasRole(['super-admin', 'admin'])) {
                return $next($request);
            }
            if (Auth::user()->role == 'admin') {
                return $next($request);
            }

            abort(403, "User does not have the correct role");
        }
        abort(401);
    }
}
