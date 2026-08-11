<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::get('/health/ready', function () {
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $db = true;
    } catch (\Exception $e) {
        $db = false;
    }

    try {
        \Illuminate\Support\Facades\Redis::ping();
        $redis = true;
    } catch (\Exception $e) {
        $redis = false;
    }

    return response()->json([
        'status' => $db && $redis ? 'ok' : 'error',
        'database' => $db,
        'redis' => $redis,
    ], $db && $redis ? 200 : 500);
});

require __DIR__.'/api.php';
