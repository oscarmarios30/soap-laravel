<?php

// app/Http/Controllers/WalletController.php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function registerClient(Request $request)
    {
        $validated = $request->validate([
            'document' => 'required|unique:clients',
            'name' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'required',
        ]);

        $client = Client::create($validated);

        Wallet::create([
            'client_id' => $client->id,
            'balance' => 0
        ]);

        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Cliente registrado exitosamente.',
            'data' => $client
        ]);
    }

    public function rechargeWallet(Request $request)
    {
        $client = Client::where('document', $request->document)
                        ->where('phone', $request->phone)
                        ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Cliente no encontrado.',
            ]);
        }

        $wallet = $client->wallet;
        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Recarga exitosa.',
            'data' => $wallet
        ]);
    }

    public function pay(Request $request)
    {
        $client = Client::where('document', $request->document)->first();

        if (!$client || $client->wallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'cod_error' => '02',
                'message_error' => 'Saldo insuficiente o cliente no encontrado.',
            ]);
        }

        // Generar token y simular envío por correo
        $token = rand(100000, 999999);
        $sessionId = uniqid();

        // Simulación de enviar token al correo
        // Mail::to($client->email)->send(new PaymentTokenMail($token));

        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Token enviado exitosamente.',
            'data' => [
                'sessionId' => $sessionId,
                'token' => $token
            ]
        ]);
    }

    public function confirmPayment(Request $request)
    {
        // Aquí se simula la validación del token y el sessionId para confirmar el pago
        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Pago confirmado exitosamente.',
        ]);
    }

    public function checkBalance(Request $request)
    {
        $client = Client::where('document', $request->document)
                        ->where('phone', $request->phone)
                        ->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Cliente no encontrado.',
            ]);
        }

        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Consulta exitosa.',
            'data' => [
                'balance' => $client->wallet->balance
            ]
        ]);
    }
}
