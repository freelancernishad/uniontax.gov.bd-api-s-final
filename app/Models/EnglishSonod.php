<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnglishSonod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'english_sonods';  // Table name (optional if it follows convention)

    // Mass assignable attributes
    protected $fillable = [
        'sonod_id',
        'unioun_name',
        'year',
        'sonod_Id',
        'uniqeKey',
        'image',
        'sonod_name',
        'successor_father_name',
        'successor_mother_name',
        'ut_father_name',
        'ut_mother_name',
        'ut_grame',
        'ut_post',
        'ut_thana',
        'ut_district',
        'ut_word',
        'successor_father_alive_status',
        'successor_mother_alive_status',
        'applicant_holding_tax_number',
        'applicant_national_id_number',
        'applicant_birth_certificate_number',
        'applicant_passport_number',
        'applicant_date_of_birth',
        'family_name',
        'Annual_income',
        'Annual_income_text',
        'Subject_to_permission',
        'disabled',
        'The_subject_of_the_certificate',
        'Name_of_the_transferred_area',
        'applicant_second_name',
        'applicant_owner_type',
        'applicant_name_of_the_organization',
        'organization_address',
        'applicant_name',
        'utname',
        'ut_religion',
        'alive_status',
        'applicant_gender',
        'applicant_marriage_status',
        'applicant_vat_id_number',
        'applicant_tax_id_number',
        'applicant_type_of_business',
        'applicant_type_of_businessKhat',
        'applicant_type_of_businessKhatAmount',
        'applicant_father_name',
        'applicant_mother_name',
        'applicant_occupation',
        'applicant_education',
        'applicant_religion',
        'applicant_resident_status',
        'applicant_present_village',
        'applicant_present_road_block_sector',
        'applicant_present_word_number',
        'applicant_present_district',
        'applicant_present_Upazila',
        'applicant_present_post_office',
        'applicant_permanent_village',
        'applicant_permanent_road_block_sector',
        'applicant_permanent_word_number',
        'applicant_permanent_district',
        'applicant_permanent_Upazila',
        'applicant_permanent_post_office',
        'successor_list',
        'khat',
        'last_years_money',
        'currently_paid_money',
        'total_amount',
        'amount_deails',
        'the_amount_of_money_in_words',
        'applicant_mobile',
        'applicant_email',
        'applicant_phone',
        'applicant_national_id_front_attachment',
        'applicant_national_id_back_attachment',
        'applicant_birth_certificate_attachment',
        'prottoyon',
        'sec_prottoyon',
        'stutus',
        'payment_status',
        'chaireman_name',
        'chaireman_type',
        'c_email',
        'chaireman_sign',
        'socib_name',
        'socib_signture',
        'socib_email',
        'cancedby',
        'cancedbyUserid',
        'pBy',
        'sameNameNew',
        'orthoBchor',
        'renewed',
        'renewed_id',
        'format'
    ];

    // Defining the relationship with the Sonod model
    public function sonod()
    {
        return $this->belongsTo(Sonod::class,'sonod_Id','id');
    }
}
