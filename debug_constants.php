<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
$request = Request::capture();
$middleware = app(HandleInertiaRequests::class);
$shared = $middleware->share($request);
echo "DEBUG_CONSTANTS: \n";
echo json_encode($shared['constants']);
echo "\n";
