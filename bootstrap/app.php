<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Faire confiance à tous les reverse proxies (Render)
        $middleware->trustProxies(at: '*');

        // Réactiver la vérification CSRF (maintenant que le proxy est configuré)
        $middleware->validateCsrfTokens(except: [
            // Retirez le '*' pour sécuriser à nouveau l'application
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();