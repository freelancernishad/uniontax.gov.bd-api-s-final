<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoldingBokeya extends Model
{
    use HasFactory;

    protected $fillable = [
        'holdingTax_id',
        'year',
        'price',
        'payYear',
        'payOB',
        'status',
    ];

    public function holdingPayments()
    {
        return $this->hasMany(Payment::class, 'sonodId');
    }
}