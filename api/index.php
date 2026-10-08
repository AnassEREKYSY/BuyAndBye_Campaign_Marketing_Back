<?php

// Vercel entry point: every request is routed here (see vercel.json) and handed to Laravel.

// Serverless defaults. Values set in the Vercel dashboard always win; these only fill the gaps
// (the "env" block of vercel.json is not applied to the PHP runtime).
$defaults = [
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_CONFIG_CACHE' => '/tmp/config.php',
    'APP_EVENTS_CACHE' => '/tmp/events.php',
    'APP_PACKAGES_CACHE' => '/tmp/packages.php',
    'APP_ROUTES_CACHE' => '/tmp/routes.php',
    'APP_SERVICES_CACHE' => '/tmp/services.php',
    'VIEW_COMPILED_PATH' => '/tmp',
    'CACHE_STORE' => 'array',
    'CACHE_DRIVER' => 'array',
    'SESSION_DRIVER' => 'array',
    'QUEUE_CONNECTION' => 'sync',
    'BROADCAST_DRIVER' => 'null',
    'LOG_CHANNEL' => 'stderr',
    'DB_CONNECTION' => 'pgsql',
    'DB_SSLMODE' => 'require',
    'DB_SCHEMA' => 'kickback',
    'DB_EMULATE_PREPARES' => 'true',
];
foreach ($defaults as $key => $value) {
    if (getenv($key) === false && ! isset($_ENV[$key]) && ! isset($_SERVER[$key])) {
        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// This file lives in /api, so Laravel would treat "/api" as the app's base path and turn
// /api/v1/... into /v1/... (404). Pretend the front controller sits at the web root instead.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/../public/index.php';
