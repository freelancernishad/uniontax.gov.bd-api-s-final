<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'holding_id',
        'name',
        'relation',
        'age',
        'gender',
        'nid_no',
        'birth_certificate_no',
        'mobile_no',
        'occupation',
        'education',
        'disability',
    ];

    public function holding()
    {
        return $this->belongsTo(Holdingtax::class, 'holding_id');
    }

    public function sohayotas()
    {
        return $this->hasMany(SohayotaBiboron::class, 'family_member_id');
    }
}
