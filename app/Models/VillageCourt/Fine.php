<?php

namespace App\Models\VillageCourt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fine extends Model {
    use HasFactory;
    protected $fillable = ['case_id', 'fine_amount', 'payment_status', 'union_name'];

    public function villageCourtCase() {
        return $this->belongsTo(VillageCourtCase::class, 'case_id');
    }
}
