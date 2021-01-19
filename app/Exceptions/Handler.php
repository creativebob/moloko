<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;

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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        // Пробуем замутить исключение----------------------------------

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {

            Auth::logout();
            return redirect()->route('login');

            // return response()->view('errors.unauthorized', [], 403);
        }

        // if ($exception instanceof \Illuminate\Auth\Access\AuthorizationException) {

        //     dd('Проблемы с правами, Бро!');
        //     return response()->view('errors.unauthorized', [], 403);
        // }

        // Конец пробы -------------------------------------------------

        // Исключение 404, гугл кешировал ктраницы. и на ВК нельзя на них попасть, нужно отдавать 404
//        if ($this->isHttpException($exception)) {
//            $path = $exception->getMessage() . '.errors.' . $exception->getStatusCode();
//            if (view()->exists($path)) {
//                return response()->view($path);
//            }
//        }

        return parent::render($request, $exception);
    }

}
