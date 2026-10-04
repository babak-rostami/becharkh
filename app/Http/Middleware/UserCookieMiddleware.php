<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserCookieMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('user')->check()) {
            $user = auth('user')->user();
            $cookies = $user->setUserCookies();
        } else {
            $cookies = [];
        }

        $response = $next($request);

        foreach ($cookies as $cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }
}
