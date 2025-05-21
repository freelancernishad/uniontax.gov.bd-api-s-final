<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeLicenseKhat extends Model
{
    use HasFactory;

    protected $fillable = [
        'khat_id',
        'name',
        'main_khat_id',
        'type',
    ];


    public function khatFees()
    {
        return $this->hasMany(TradeLicenseKhatFee::class, 'khat_id_1', 'khat_id');
    }

        // Get parent khat
    public function parentKhat()
    {
        return $this->belongsTo(TradeLicenseKhat::class, 'main_khat_id', 'khat_id');
    }

    // Get child khats
    public function childKhats()
    {
        return $this->hasMany(TradeLicenseKhat::class, 'main_khat_id', 'khat_id');
    }


}
