<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintanceFee extends Model
{
    protected $fillable = [
        'union',
        'amount',
        'transaction_fee',
        'status',
        'payment_date',
        'type',
        'period',
        'trx_id',
    ];

    // Automatically calculate and set transaction fee if not set
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->amount && !$model->transaction_fee) {
                $model->transaction_fee = round($model->amount * 0.015, 2);
            }
        });
    }
}
