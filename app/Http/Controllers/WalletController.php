<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function registerClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Faltan campos requeridos o son inválidos.',
                'errors' => $validator->errors()
            ], 400);
        }

        $existingClient = Client::where('document', $request->document)
                                ->orWhere('email', $request->email)
                                ->orWhere('phone', $request->phone)
                                ->first();

        if ($existingClient) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Cliente ya registrado con el documento, email o teléfono proporcionado.',
            ]);
        }

        $client = Client::create($validator->validated());

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
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'phone' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Faltan campos requeridos o son inválidos.',
                'errors' => $validator->errors()
            ], 400);
        }

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
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Faltan campos requeridos o son inválidos.',
                'errors' => $validator->errors()
            ], 400);
        }

        $client = Client::where('document', $request->document)->first();

        if (!$client || $client->wallet->balance < $request->amount) {
            return response()->json([
                'success' => false,
                'cod_error' => '02',
                'message_error' => 'Saldo insuficiente o cliente no encontrado.',
            ]);
        }

        $token = rand(100000, 999999);
        $sessionId = uniqid();

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
        return response()->json([
            'success' => true,
            'cod_error' => '00',
            'message_error' => 'Pago confirmado exitosamente.',
        ]);
    }

    public function checkBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'cod_error' => '01',
                'message_error' => 'Faltan campos requeridos o son inválidos.',
                'errors' => $validator->errors()
            ], 400);
        }

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
