<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [

    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [

    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
        'new_password',
        'new_password_confirmation',
    ];

    /** Register the exception handling callbacks for the application. */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });

        $this->reportable(function (FileNotFoundException $e) {
            abort(404, trans('File not found.'));
        });
    }

    protected function unauthenticated(
        $request, AuthenticationException $exception
    ): Response|JsonResponse|RedirectResponse {
        return $this->shouldReturnJson($request, $exception)
            ? parent::unauthenticated($request, $exception)
            : redirect()->guest(
                $exception->redirectTo()
                    ?? route('filament.admin.auth.login')
            );
    }
}
