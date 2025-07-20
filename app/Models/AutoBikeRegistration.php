<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
