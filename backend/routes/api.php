<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandController;
use App\Http\Controllers\Api\ReportController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:api')->group(function () {
// CLients
    Route::apiResource('clients', ClientController::class)->only(['index', 'store', 'update']);

// Demands
    Route::patch('demands/{demand}/status', [DemandController::class, 'updateStatus']);
    Route::apiResource('demands', DemandController::class)->only(['index', 'store', 'update']);

// Report
    Route::get('reports/clients/{client}', [ReportController::class, 'monthlyByClient']);
});
