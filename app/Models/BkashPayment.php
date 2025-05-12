<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BkashPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'id_token',
        'invoice',
        'amount',
        'status',
    ];
}
