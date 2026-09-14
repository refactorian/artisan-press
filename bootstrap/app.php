<?php

use App\Http\Middleware\RedirectMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            RedirectMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
                $message = $e->getMessage() ?: 'An unexpected server error occurred.';

                if ($statusCode === 500 && ! config('app.debug')) {
                    $message = 'Server Error. Please try again later.';
                }

                $response = [
                    'status' => 'error',
                    'message' => $message,
                    'code' => $statusCode,
                ];

                if ($e instanceof ValidationException) {
                    $response['errors'] = $e->errors();
                }

                return response()->json($response, $statusCode);
            }
        });
    })->create();
