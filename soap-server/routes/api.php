<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('customer', [CustomerController::class, 'store']);
Route::post('transaction/charge-balance', [TransactionController::class, 'chargeBalance']);
Route::post('transaction/request-payment', [TransactionController::class, 'requestPayment']);
Route::post('transaction/confirm-payment', [TransactionController::class, 'confirmPayment']);
Route::post('wallet/check-balance', [WalletController::class, 'checkBalance']);
