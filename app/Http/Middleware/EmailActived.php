<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmailActived
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
        if (auth('user')->user()->email_actived == 0) {
            return redirect()->route('user.dashboard',auth('user')->user()->username)->with('success', 'برای ثبت آگهی ایمیل خود را تایید کنید');
        } else {
            return $next($request);
        }
    }
}
