<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessDirectory extends Model
{
    use HasFactory;

    protected $fillable = [
        'union_name',
        'applicant_owner_type',
        'applicant_name_of_the_organization',
        'organization_address',
        'applicant_occupation',
        'applicant_vat_id_number',
        'applicant_tax_id_number',
        'applicant_type_of_businessKhat',
        'applicant_type_of_businessKhatAmount',
        'last_years_money',
        'applicant_type_of_business',
        'name',
        'gender',
        'father_name',
        'mother_name',
        'nid_no',
        'birth_id_no',
        'mobile_no',
        'applicant_holding_tax_number',
        'holding_owner_name',
        'holding_owner_relationship',
        'holding_owner_mobile',
        'applicant_date_of_birth',
        'applicant_religion',
        'status',
    ];
}
