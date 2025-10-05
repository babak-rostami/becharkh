<?php

namespace App\Http\Middleware;

use App\Models\PageError;
use Closure;
use Illuminate\Http\Request;
use Throwable;

class PageLogErrors
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
        try {
            return $next($request);
        } catch (Throwable $e) {
            $page_error = new PageError();
            $page_error->url = $request->fullUrl();
            $page_error->method = $request->method();
            $page_error->message = $e->getMessage();
            $page_error->stack_trace = $e->getTraceAsString();
            $page_error->save();

            throw $e;
        }
    }
}
