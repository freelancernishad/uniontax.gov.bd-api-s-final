<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SohayotaBiboron extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_member_id',
        'sohayota_type',
        'card_number',
        'start_date',
        'end_date',
        'status',
        'remarks',
    ];

    public function familyMember()
    {
        return $this->belongsTo(FamilyMember::class, 'family_member_id');
    }
}
