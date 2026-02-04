<?php

use App\Http\Controllers\Api\ClientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DemandController;
use App\Http\Controllers\Api\ReportController;

// CLients
Route::apiResource('clients', ClientController::class)->only(['index', 'store', 'update']);

// Demands
Route::patch('demands/{demand}/status', [DemandController::class, 'updateStatus']);
Route::apiResource('demands', DemandController::class)->only(['index', 'store', 'update']);

// Report
Route::get('reports/clients/{client}', [ReportController::class, 'monthlyByClient']);
