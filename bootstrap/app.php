<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add API rate limiting
        $middleware->api([
            'throttle:api'
        ]);
        
        // Add CSRF protection for web routes
        $middleware->web([
            'throttle:web'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
