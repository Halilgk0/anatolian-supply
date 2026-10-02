<?php

use App\Http\Controllers\MediaController;
use App\Http\Middleware\MigrateOnFirstRequest;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Outside the web group so photo responses carry no session cookie and can be cached by the CDN.
            Route::get('/media/{path}', MediaController::class)->where('path', '.*')->name('media.show');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Behind Vercel every request arrives over plain HTTP with the original scheme in X-Forwarded-Proto.
        $middleware->trustProxies(at: '*');

        $middleware->append(MigrateOnFirstRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
