<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeLicenseKhatFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'khat_fee_id',
        'khat_id_1',
        'khat_id_2',
        'fee',
    ];

    public function khat1()
    {
        return $this->belongsTo(TradeLicenseKhat::class, 'khat_id_1', 'khat_id');
    }

    public function khat2()
    {
        return $this->belongsTo(TradeLicenseKhat::class, 'khat_id_2', 'khat_id');
    }
}
