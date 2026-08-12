<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot(): void
{
    // Force le HTTPS si l'application est consultée via un proxy sécurisé (comme ngrok)
if (Request::header('X-Forwarded-Proto') === 'https') {
        URL::forceScheme('https');
    }
}
}
