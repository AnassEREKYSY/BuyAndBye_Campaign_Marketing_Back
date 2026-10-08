<?php

// Vercel entry point: every request is routed here (see vercel.json) and handed to Laravel.
// This file lives in /api, so Laravel would treat "/api" as the app's base path and turn
// /api/v1/... into /v1/... (404). Pretend the front controller sits at the web root instead.
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

require __DIR__ . '/../public/index.php';
