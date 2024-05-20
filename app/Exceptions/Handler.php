<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error' => 'the ' .substr($exception->getModel() , 11) .' you have asked for is not found.'], 404);
        }

        if ($exception instanceof NotFoundHttpException) {
            return response()->json(['error' => "Page Not Found. the route/link you have entered is not valid"], 404);
        }

        if ($exception instanceof AuthenticationException) {
            return response()->json(['error' => 'Unauthorized user , you have to enter valid token'], 401);
        }

        return parent::render($request, $exception);
    }
}
