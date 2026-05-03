<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::apiResource('orders', OrderController::class)
        ->middleware('prevent-duplicate-request')
        ->only(['store', 'show', 'index']);
});

Route::prefix('orders/{order}')->controller(OrderController::class)->group(function () {
    Route::post('/processing', 'processing')->name('api.orders.processing');
    Route::post('/complete', 'complete')->name('api.orders.complete');
    Route::post('/cancel', 'cancel')->name('api.orders.cancel');
});
