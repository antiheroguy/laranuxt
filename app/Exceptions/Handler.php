<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
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
        $this->renderable(function (AuthenticationException $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        });

        $this->renderable(function (QueryException $e) {
            return response()->json(['message' => 'Cannot execute query'], 400);
        });

        $this->renderable(function (NotFoundHttpException $e) {
            return response()->json(['message' => 'Route not found'], 404);
        });

        $this->renderable(function (ModelNotFoundException $e) {
            return response()->json(['message' => 'Model not found'], 404);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        });

        $this->renderable(function (Exception $e) {
            return response()->json(
                ['message' => $e->getMessage()],
                method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500
            );
        });
    }
}
