<?php

// routes/web.php
use App\Http\Controllers\WalletController;

Route::post('/soap/register', [WalletController::class, 'registerClient']);
Route::post('/soap/recharge', [WalletController::class, 'rechargeWallet']);
Route::post('/soap/pay', [WalletController::class, 'pay']);
Route::post('/soap/confirm-payment', [WalletController::class, 'confirmPayment']);
Route::post('/soap/balance', [WalletController::class, 'checkBalance']);
