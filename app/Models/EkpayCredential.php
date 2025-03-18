<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EkpayCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'mer_pas_key',
        'api_key',
        'base_url',
        'callback_url',
        'whitelistip',
    ];
}
