<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/pay-by-card', [PaymentController::class, 'payByCard'])->name('payment.card');
