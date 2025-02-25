<?php

namespace App\Models\VillageCourt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillageCourtCase extends Model {
    use HasFactory;
    protected $fillable = [
        'case_number', 'applicant_name', 'applicant_father_husband_name', 'applicant_address', 'applicant_mobile',
        'defendant_name', 'defendant_father_husband_name', 'defendant_address', 'defendant_mobile',
        'case_type', 'case_details', 'application_date', 'case_status', 'case_register_number', 'order_sheet_details', 'union_name'
    ];

    public function summons() {
        return $this->hasMany(Summon::class);
    }

    public function nominations() {
        return $this->hasMany(Nomination::class, 'case_id');
    }

    public function decrees() {
        return $this->hasMany(Decree::class, 'case_id');
    }

    public function attendances() {
        return $this->hasMany(Attendance::class, 'case_id');
    }

    public function fees() {
        return $this->hasMany(Fee::class, 'case_id');
    }

    public function caseTransfers() {
        return $this->hasMany(CaseTransfer::class, 'case_id');
    }

    public function fines() {
        return $this->hasMany(Fine::class, 'case_id');
    }
}
