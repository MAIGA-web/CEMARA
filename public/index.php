<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;


define('LARAVEL_START', microtime(true));

// Check If The Application Is Under Maintenance...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register The Auto Loader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
