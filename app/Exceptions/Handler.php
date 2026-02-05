<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Src\Domain\Auth\Exceptions\AuthException;
use Src\Domain\Auth\Exceptions\InvalidCredentialsException;
use Src\Domain\Users\Exceptions\InvalidRoleTransitionException;
use Src\Domain\Users\Exceptions\UserAlreadyExistsException;
use Src\Domain\Users\Exceptions\UserAlreadySellerException;
use Src\Domain\Users\Exceptions\UserException;
use Src\Domain\Users\Exceptions\UserNotFoundException;
use Src\Domain\Products\Exceptions\ProductException;
use Src\Domain\Products\Exceptions\ProductNotFoundException;
use Throwable;

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

        // Handle domain exceptions with user-friendly responses
        $this->renderable(function (Throwable $e) {
            return $this->handleDomainException($e);
        });
    }

    /**
     * Handle domain exceptions and return user-friendly JSON responses.
     */
    private function handleDomainException(Throwable $e): ?JsonResponse
    {
        // User not found (404)
        if ($e instanceof UserNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
                'error' => 'The requested user does not exist'
            ], 404);
        }

        // User already exists (409 - Conflict)
        if ($e instanceof UserAlreadyExistsException) {
            return response()->json([
                'success' => false,
                'message' => 'User already exists',
                'error' => 'An account with this email address already exists'
            ], 409);
        }

        // User already seller (400 - Bad Request)
        if ($e instanceof UserAlreadySellerException) {
            return response()->json([
                'success' => false,
                'message' => 'Already a seller',
                'error' => 'Your account is already registered as a seller'
            ], 400);
        }

        // Invalid role transition (400 - Bad Request)
        if ($e instanceof InvalidRoleTransitionException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid role transition',
                'error' => 'Only buyer accounts can transition to seller role'
            ], 400);
        }

        // Invalid credentials (401 - Unauthorized)
        if ($e instanceof InvalidCredentialsException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'error' => 'The provided email or password is incorrect'
            ], 401);
        }

        // Product not found (404)
        if ($e instanceof ProductNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => 'The requested product does not exist'
            ], 404);
        }

        // Generic User Exception (400)
        if ($e instanceof UserException) {
            return response()->json([
                'success' => false,
                'message' => 'User error',
                'error' => $e->getMessage()
            ], 400);
        }

        // Generic Auth Exception (401)
        if ($e instanceof AuthException) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication error',
                'error' => $e->getMessage()
            ], 401);
        }

        // Generic Product Exception (400)
        if ($e instanceof ProductException) {
            return response()->json([
                'success' => false,
                'message' => 'Product error',
                'error' => $e->getMessage()
            ], 400);
        }

        // Let Laravel handle other exceptions (validation, etc.)
        return null;
    }
}
