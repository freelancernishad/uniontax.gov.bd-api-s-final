<?php

namespace App\Models\VillageCourt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model {
    use HasFactory;
    protected $fillable = ['case_id', 'person_name', 'role', 'date', 'union_name'];

    public function villageCourtCase() {
        return $this->belongsTo(VillageCourtCase::class, 'case_id');
    }
}
