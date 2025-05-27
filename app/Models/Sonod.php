<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sonod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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
        'format',
        'hasEnData',
        'font_family', // Add this line
        'cancel_reason',
        'signboard_type',
        'signboard_size_square_fit',
        'chalan_traking_no',
        'chalan_date',
        'chalan_amount',
    ];

    protected $attributes = [
        'font_family' => 'bangla', // Set default value
    ];

    // Relationship: A Sonod has many payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'sonodId');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'sonodId')
            ->orderBy('created_at'); // Order by creation date to get the first one
    }

    // Get Payment details as array
    public function getPaymentsDetailsAttribute()
    {
        return $this->payments->map(function ($payment) {
            return [
                'amount' => $payment->amount,
                'status' => $payment->status,
                'date' => $payment->created_at->format('Y-m-d'),
            ];
        });
    }

    public function english_sonod()
    {
        return $this->hasOne(EnglishSonod::class,'sonod_Id','id');
    }



    public static function generateSonodId($union, $sonodname, $orthoBchor)
    {
        // Determine the fiscal year
        $sortYear = (date('m') < 7) ? date('y') - 1 : date('y');

        // Retrieve union info in one query
        $unionInfo = Uniouninfo::where('short_name_e', $union)->first();
        if (!$unionInfo) {
            return null; // Return null if union info doesn't exist
        }

        // Retrieve the latest sonod for the given union, sonod name, and orthoBchor
        $latestSonod = self::where([
            'unioun_name' => $union,
            'sonod_name' => $sonodname,
            'orthoBchor' => $orthoBchor,
        ])->latest()->first();

        // Generate the sonod_id
        if ($latestSonod) {
            // Increment the latest sonod_id
            $sonodFinalId = $latestSonod->sonod_Id + 1;
        } else {
            // Default sonod_id if no previous sonods are found
            $sonodId = str_pad(1, 5, '0', STR_PAD_LEFT); // Start from 00001
            $sonodFinalId = $unionInfo->u_code . $sortYear . $sonodId;
        }

        return $sonodFinalId;
    }



    public function holdingOwners()
    {
        return $this->hasOne(SonodHoldingOwner::class, 'sonod_id');
    }


    public function files()
    {
        return $this->hasMany(SonodFile::class, 'sonod_id');
    }


}

