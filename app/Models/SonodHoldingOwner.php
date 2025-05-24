<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SonodHoldingOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'sonod_id',
        'holding_no',
        'name',
        'mobile',
        'relationship',
    ];

    public function sonod()
    {
        return $this->belongsTo(Sonod::class, 'sonod_id');
    }
}
