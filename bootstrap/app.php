<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use App\Http\Middleware\PreventDuplicateRequest;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'prevent-duplicate-request' => PreventDuplicateRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e): void {
            if (!config('app.debug')) {
                Log::error('Unhandled exception', ['exception' => $e]);
            }
        });

        $exceptions->render(function (Throwable $e, $request) {
            if (config('app.debug')) {
                return null; 
            }

            if ($e instanceof HttpExceptionInterface || $e instanceof ValidationException) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'An error occurred. Please try again.',
                ], 500);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred. Please try again.']);
        });
    })->create();
