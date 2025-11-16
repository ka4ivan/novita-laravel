<?php

use Illuminate\Support\Facades\Route;

// AUTH
// SOCIALITE
Route::post('socialite/{provider}', [\App\Http\Auth\Api\Controllers\SocialiteController::class, 'socialLogin'])->name('socialite.oauth');

Route::post('logout', [\App\Http\Auth\Api\Controllers\LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::group(['middleware' => [\App\Http\Middleware\SetClientDomain::class]], function () {

    // APP
    Route::get('app/glob', [\App\Http\Client\Controllers\AppController::class, 'glob']);

    // AI
    Route::group(['prefix' => 'ai'], function () {
        Route::get('models', [\App\Http\Client\Controllers\AIModelController::class, 'models']);

        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('txt2img', [\App\Http\Client\Controllers\AIController::class, 'txt2img']);
            Route::post('img2img', [\App\Http\Client\Controllers\AIController::class, 'img2img']);

            // MY AI
            Route::get('my/models', [\App\Http\Client\Controllers\My\AIModelController::class, 'index']);
            Route::post('my/models', [\App\Http\Client\Controllers\My\AIModelController::class, 'store']);
        });
    });

    // MEDIA
    Route::post('media', [\App\Http\Client\Controllers\MediaController::class, 'upload']);
    Route::get('media/{media}', [\App\Http\Client\Controllers\MediaController::class, 'download']);
    Route::delete('media/{media}', [\App\Http\Client\Controllers\MediaController::class, 'delete']);

    Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'my'], function () {
        // PROFILE
        Route::get('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'edit']);
        Route::post('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'update']);
        Route::delete('profile', [\App\Http\Client\Controllers\My\ProfileController::class, 'delete']);

        // AI
        Route::get('ai/jobs', [\App\Http\Client\Controllers\My\AIJobController::class, 'index']);

        // FAVORITES
        Route::group(['prefix' => 'favorites'], function () {
            Route::get('medias', [\App\Http\Client\Controllers\My\FavoriteController::class, 'medias']);
            Route::post('medias/{media}', [\App\Http\Client\Controllers\My\FavoriteController::class, 'media']);
        });
    });
});
