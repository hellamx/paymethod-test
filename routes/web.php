<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/pay-by-card', [PaymentController::class, 'payByCard'])->name('payment.card');
Route::post('/pay-by-sbp', [PaymentController::class, 'payBySbp'])->name('payment.sbp');
Route::post('/pay-by-plate', [PaymentController::class, 'payByPlate'])->name('payment.plate');



// TODO
Route::get('/testPaySbp/{sbpId}/{orderId}', [PaymentController::class, 'testPaySbp']);
