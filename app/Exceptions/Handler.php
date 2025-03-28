<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
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
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // if ($request->is('*/resident/v1/*')) {
        if ($request->is('*/user/v1/*')) {
            // token  missing
            if (!$request->bearerToken()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is missing !',
                ], 401);
            }
            // token invalid or expired
            if (!Auth::guard('sanctum')->check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Token !',
                ], 401);
            }
        }
    }

}
