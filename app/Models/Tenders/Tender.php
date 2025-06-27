<?php

namespace App\Models\Tenders;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tender extends Model
{
    use HasFactory;
    protected $fillable = [
        'tender_id',
        'dorId',
        'nidNo',
        'nidDate',
        'applicant_orgName',
        'applicant_org_fatherName',
        'vill',
        'postoffice',
        'thana',
        'distric',
        'mobile',
        'DorAmount',
        'DorAmountText',
        'depositAmount',
        'bank_draft_image',
        'deposit_details',
        'status',
        'payment_status',
    ];


        // 🔄 Auto-generate dorId when creating
    protected static function booted()
    {
        static::creating(function ($tender) {
            if (!$tender->dorId && $tender->tender_id) {
                $count = self::where('tender_id', $tender->tender_id)->count();
                $tender->dorId = ($tender->tender_id * 100000) + ($count + 1);
            }
        });
    }



    /**
     * Upload bank draft image to S3 and return the URL
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int|string $tenderId
     * @return string|null
     */
    public static function uploadBankDraftImage($file, $tenderId)
    {
        $dateFolder = date('Y-m-d');
        $filePath = "tenders/bank_draft/{$tenderId}/{$dateFolder}";

        $fileName = 'draft-' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        // Assume you have helper function uploadDocumentsToS3($file, $filePath, $dateFolder, $id = null)
        $url = uploadDocumentsToS3($file, $filePath, $dateFolder, $tenderId, $fileName);

        return $url ?: null;
    }

    /**
     * Accessor for bank_draft_image to return full URL from S3
     */
    public function getBankDraftImageAttribute($value)
    {
        return $value ? getUploadDocumentsToS3($value) : null;
    }


}
