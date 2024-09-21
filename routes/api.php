<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WalletController;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Ruta para registrar un cliente
Route::post('/register', [WalletController::class, 'registerClient']);

// Ruta para recargar saldo de la billetera
Route::post('/recharge', [WalletController::class, 'rechargeWallet']);

// Ruta para realizar un pago
Route::post('/pay', [WalletController::class, 'pay']);

// Ruta para confirmar un pago
Route::post('/confirm-payment', [WalletController::class, 'confirmPayment']);

// Ruta para consultar el saldo de la billetera
Route::post('/balance', [WalletController::class, 'checkBalance']);

// Ruta para obtener el usuario autenticado
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
