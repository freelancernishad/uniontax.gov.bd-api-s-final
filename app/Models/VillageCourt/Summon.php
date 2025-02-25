<?php

namespace App\Models\VillageCourt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Summon extends Model {
    use HasFactory;
    protected $fillable = ['village_court_case_id', 'summon_type', 'person_name', 'address', 'mobile', 'summon_date', 'summon_number', 'delivery_status', 'union_name'];

    public function villageCourtCase() {
        return $this->belongsTo(VillageCourtCase::class);
    }
}
