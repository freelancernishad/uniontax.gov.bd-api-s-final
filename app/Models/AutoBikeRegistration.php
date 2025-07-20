<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoBikeRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year',
        'application_type',
        'applicant_name_bn',
        'applicant_name_en',
        'applicant_father_name',
        'applicant_mother_name',
        'applicant_gender',
        'nationality',
        'applicant_religion',
        'applicant_date_of_birth',
        'marital_status',
        'profession',
        'blood_group',
        'applicant_mobile',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'emergency_contact_national_id_number',
        'auto_bike_purchase_date',
        'auto_bike_last_renew_date',
        'auto_bike_supplier_name',
        'auto_bike_supplier_address',
        'auto_bike_supplier_mobile',
        'passport_photo',
        'national_id_copy',
        'auto_bike_receipt',
        'previous_license_copy',
        'affidavit_copy',
        'status',         // NEW
        'union_name',     // NEW
    ];
}
