<?php

namespace App\Exceptions;

use App\Models\PageError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function report(Throwable $exception)
    {
        $statusCode = 500;
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
        } elseif ($exception instanceof HttpResponseException && $exception->getResponse()) {
            $statusCode = $exception->getResponse()->getStatusCode();
        }

        $page_error = new PageError();
        $page_error->url = request()->fullUrl();
        $page_error->method = request()->method();
        $page_error->message = $exception->getMessage();
        $page_error->stack_trace = $exception->getTraceAsString();
        $page_error->status_code = $statusCode;
        $page_error->ip_address = request()->ip();
        $page_error->user_agent =  request()->header('User-Agent');;
        $page_error->save();

        parent::report($exception);
    }
}
