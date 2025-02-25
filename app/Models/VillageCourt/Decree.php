<?php

namespace App\Models\VillageCourt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decree extends Model {
    use HasFactory;
    protected $fillable = ['case_id', 'decree_details', 'issued_by', 'date_issued', 'union_name'];

    public function villageCourtCase() {
        return $this->belongsTo(VillageCourtCase::class, 'case_id');
    }
}
