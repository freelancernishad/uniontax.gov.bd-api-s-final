<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFailed extends Model
{
    use HasFactory;

    // Fields that can be mass-assigned
    protected $fillable = [
        'union_name',
        'certificate',
        'payment_method',
        'account_number',
        'amount',
        'transaction_id',
        'sonod_id',
        'details',
        'transId',
        'status',
        'comment',
        'datetime',
    ];

    // Cast the `datetime` field to a Carbon instance
    protected $dates = [
        'datetime',
    ];
}
