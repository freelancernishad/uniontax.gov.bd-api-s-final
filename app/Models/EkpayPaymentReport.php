<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EkpayPaymentReport extends Model
{
    protected $fillable = [
        'union',
        'start_date',
        'end_date',
        'ekpay_amount',
        'server_amount',
        'difference_amount',
    ];
}
