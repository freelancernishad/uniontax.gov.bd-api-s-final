<?php

// app/Models/MaintanceFee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintanceFee extends Model
{
    protected $fillable = [
        'union',
        'amount',
        'status',
        'payment_date',
        'type',
        'trx_id',
    ];
}
