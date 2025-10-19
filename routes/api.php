<?php

use Illuminate\Support\Facades\Route;

// AUTH
// SOCIALITE
Route::post('socialite/{provider}', [\App\Http\Auth\Api\Controllers\SocialiteController::class, 'socialLogin'])->name('socialite.oauth');


Route::group(['middleware' => [\App\Http\Middleware\SetClientDomain::class]], function () {

    // AI
    Route::group(['prefix' => 'ai'], function () {
        Route::get('models', [\App\Http\Client\Controllers\AIModelController::class, 'models']);
    });

    // MEDIA
    Route::post('media', [\App\Http\Client\Controllers\MediaController::class, 'upload']);
    Route::delete('media/{media}', [\App\Http\Client\Controllers\MediaController::class, 'delete']);

    Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'my'], function () {
        // PROFILE
        Route::get('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'edit']);
        Route::post('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'update']);
        Route::delete('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'delete']);
    });
});
