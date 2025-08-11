<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     * Convert exceptions to JSON response for API requests.
     */
    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            if ($exception instanceof ValidationException) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $exception->errors()
                ], 422);
            }
            if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => 'Resource not found'
                ], 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return response()->json([
                    'message' => 'Method not allowed'
                ], 405);
            }
            return response()->json([
                'message' => $exception->getMessage(),
                'trace' => config('app.debug') ? $exception->getTrace() : []
            ], 500);
        }
        return parent::render($request, $exception);
    }
}
