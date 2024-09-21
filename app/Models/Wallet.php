<?php

// app/Models/Wallet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'balance',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
