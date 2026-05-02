<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});




Route::resource('orders', OrderController::class)
->only(['store', 'show', 'index', 'create'])
->middleware('prevent-duplicate-request');


// مسارات تغيير الحالة (Status Transitions)
Route::prefix('orders/{order}')->group(function () {
    Route::post('/pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::post('/processing', [OrderController::class, 'processing'])->name('orders.processing');
    Route::post('/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
});