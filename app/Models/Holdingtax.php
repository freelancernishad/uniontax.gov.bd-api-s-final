<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holdingtax extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'unioun',
        'holding_no',
        'maliker_name',
        'father_or_samir_name',
        'gramer_name',
        'word_no',
        'nid_no',
        'mobile_no',
        'griher_barsikh_mullo',
        'barsikh_muller_percent',
        'jomir_vara',
        'total_mullo',
        'rokhona_bekhon_khoroch',
        'prakklito_mullo',
        'reyad',
        'angsikh_prodoy_korjoggo_barsikh_mullo',
        'barsikh_vara',
        'rokhona_bekhon_khoroch_percent',
        'prodey_korjoggo_barsikh_mullo',
        'prodey_korjoggo_barsikh_varar_mullo',
        'total_prodey_korjoggo_barsikh_mullo',
        'current_year_kor',
        'bokeya',
        'total_bokeya',
        'image',
        'busnessName',

        ///////////////////////////////////
        'date_of_birth',
        'mother_name',
        'profession',
        'religion',
        'house_type',
        'social_facility',
        'sanitary_condition',
        'number_of_sons',
        'number_of_daughters',
        'house_loan',
        'land_amount',
        'homestead_amount',
        'business_capital',
        'socioeconomic_status',



    ];

    public function holdingTax()
    {
        return $this->belongsTo(HoldingBokeya::class, 'holdingTax_id', 'id');
    }

    public function holdingBokeyas()
    {
        return $this->hasMany(HoldingBokeya::class, 'holdingTax_id','id');
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class, 'holding_id');
    }


}
