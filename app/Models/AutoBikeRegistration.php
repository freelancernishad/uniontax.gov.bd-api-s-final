<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AutoBikeRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
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





            // 👇 Add these newly found missing fields
        'current_division',
        'applicant_present_district',
        'applicant_present_Upazila',
        'applicant_present_union',
        'permanent_address',
        'applicant_permanent_division',
        'applicant_permanent_district',
        'applicant_permanent_Upazila',
        'applicant_permanent_union',

        'holding_owner_name',              // হোল্ডিং মালিকের নাম
        'holding_owner_relationship',      // হোল্ডিং মালিকের সাথে সম্পর্ক
        'holding_owner_mobile',            // হোল্ডিং মালিকের মোবাইল নম্বর
        'auto_bike_last_regi_no',
        'applicant_holding_tax_number',
        'holding_tax_promanok',


    ];




     // Model boot method to auto-generate application_id
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->application_id)) {
                $model->application_id = $model->generateApplicationId();
            }
        });
    }

    public function generateApplicationId()
    {
        $union = $this->union_name;
        $year = $this->getFiscalYear();

        $unionInfo = Uniouninfo::where('short_name_e', $union)->latest()->first();
        if (!$unionInfo) {
            // fallback or throw error
            return null;
        }

        // get count of existing applications this year for this union
        $count = self::where('union_name', $union)
            ->whereYear('created_at', $year)
            ->count();

        // serial starts at 1 + count
        $serial = $count + 1;

        $serialStr = str_pad($serial, 4, '0', STR_PAD_LEFT);  // 4 digit serial, e.g. 0001

        // application_id = u_code + year (2 digits) + serial
        $yearShort = substr($year, 2, 2);  // e.g. 2025 -> 25
        return $unionInfo->u_code . $yearShort . $serialStr;
    }

    // Get current fiscal year based on date
    protected function getFiscalYear()
    {
        $month = date('m');
        $year = date('Y');
        if ($month < 7) {
            // If before July, fiscal year is previous year
            return $year - 1;
        }
        return $year;
    }


    /**
     * Upload & save document using global uploadDocumentsToS3() helper.
     * It replaces existing file of same type for this registration.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type           // field name like 'passport_photo'
     * @param string $filePath       // folder path like 'auto_bike_registration'
     * @param string $dateFolder     // optional date folder name (e.g. '2025-07-20')
     * @param int $registrationId
     * @return string|null           // uploaded file URL or null if fail
     */
    public static function uploadAndSaveFile($file, $type, $filePath, $dateFolder, $registrationId)
    {
        $registration = self::find($registrationId);
        if (!$registration) return null;

        // Optional: Delete old file from S3 if you want (helper function অথবা S3 SDK ব্যবহার করে)

        // Upload new file using global helper
        $url = uploadDocumentsToS3($file, $filePath, $dateFolder, $registrationId);

        if ($url) {
            // Save new URL/path in DB (store only path or full URL depends on your setup)
            $registration->{$type} = $url;
            $registration->save();
            return $url;
        }

        return null;
    }

    // Accessors for each file field to return full URL using getUploadDocumentsToS3()

    protected function passportPhoto(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? getUploadDocumentsToS3($value) : null);
    }

    protected function nationalIdCopy(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? getUploadDocumentsToS3($value) : null);
    }

    protected function autoBikeReceipt(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? getUploadDocumentsToS3($value) : null);
    }

    protected function previousLicenseCopy(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? getUploadDocumentsToS3($value) : null);
    }

    protected function affidavitCopy(): Attribute
    {
        return Attribute::get(fn ($value) => $value ? getUploadDocumentsToS3($value) : null);
    }

}
