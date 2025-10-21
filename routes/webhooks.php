<?php

use Illuminate\Support\Facades\Route;

Route::group(['as' => 'webhooks.', 'middleware' => \App\Http\Middleware\LogRoutes::class], function () {
    // https://site.com/webhooks/ai/handle/{novita}
    Route::any('ai/handle/{ai}', [\App\Http\Webhooks\Controllers\AIController::class, 'handle'])->name('ai.handle');
});
