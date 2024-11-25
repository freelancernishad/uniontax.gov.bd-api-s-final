<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SonodFee extends Model
{
    use HasFactory;


    protected $table = 'sonod_fees';  

    protected $fillable = [
        'unioun',
        'service_id',
        'fees',
        'sonodnamelist_id'
    ];

    // Relationship with Sonodnamelist (many-to-one)
    public function sonodnamelist()
    {
        return $this->belongsTo(Sonodnamelist::class, 'sonodnamelist_id');
    }
}

