<?php

use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandController;

Route::apiResource('clients', ClientController::class)->only(['index', 'store', 'update']);

Route::patch('demands/{demand}/status', [DemandController::class, 'updateStatus'])->name('demands.status');
Route::apiResource('demands', DemandController::class)->only(['index', 'store', 'update']);
