<?php

use App\Http\Controllers\CelestialBodyController;
use App\Http\Controllers\ProgramAchievementController;
use App\Http\Controllers\UserAchievementProgressController;
use Illuminate\Support\Facades\Route;

Route::apiResource('celestial-bodies', CelestialBodyController::class);
Route::apiResource('achievements', ProgramAchievementController::class);
Route::apiResource('progress', UserAchievementProgressController::class);
Route::get('users/{user}/progress', [UserAchievementProgressController::class, 'forUser']);
Route::get('program-map', [ProgramAchievementController::class, 'map']);
