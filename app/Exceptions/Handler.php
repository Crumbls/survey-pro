<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Foundation\Http\Exceptions\MaintenanceModeException) {
            return response()
                ->view('maintenance.down', [
                    'message' => 'Come back later.'
                ], 200)
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        // in Laravel 8.x MaintenanceModeException is deprecated and one should rely on
        // @throws \Symfony\Component\HttpKernel\Exception\HttpException
        // or
        // \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
        if (
            basename($exception->getFile()) == 'PreventRequestsDuringMaintenance.php') {
            return response()
                ->view('errors.maintenance')
                ->header('Content-Type', 'text/html; charset=utf-8');
        }

        return parent::render($request, $exception);
    }
}
