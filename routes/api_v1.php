<?php

use App\Http\Api\V1\Controllers\TemplateController;
use App\Http\Api\V1\Controllers\WorkoutController;
use Illuminate\Support\Facades\Route;

Route::get('/templates', [TemplateController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('workouts')->group(function () {
        Route::get('', [WorkoutController::class, 'index']);
        Route::get('{workout}', [WorkoutController::class, 'show']);
        Route::post('', [WorkoutController::class, 'store']);
        Route::post('replicate', [WorkoutController::class, 'replicate']);
        Route::delete('{workout}', [WorkoutController::class, 'destroy']);
    });
});
