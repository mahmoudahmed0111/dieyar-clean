<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // تسجيل middleware للغة والمصادقة
        $middleware->alias([
            'setlocale' => \App\Http\Middleware\SetLocale::class,
            'auth' => \App\Http\Middleware\Authenticate::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        ]);

        // تسجيل middleware groups
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class.':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withProviders([
        \App\Providers\RouteServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
