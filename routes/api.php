<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::name('api.')->group(function () {
        Route::get('stats', [\App\Http\Controllers\Api\StatsController::class, 'show']);
        Route::get('users', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::get('cat-fact', [\App\Http\Controllers\Api\CatController::class, 'show']);

        Route::apiResource('tasks', TaskController::class);
    });
});

