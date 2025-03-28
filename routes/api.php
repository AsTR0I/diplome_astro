<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CdrController;
use Illuminate\Support\Facades\Route;

// auth
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->get('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('calls', [CdrController::class, 'index']);
});