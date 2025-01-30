<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('user')->check()) {
            return $next($request);
        } else {
            return redirect()->route('home')->with('success', 'ابتدا وارد حساب کاربری خود شوید');
        }
    }
}
