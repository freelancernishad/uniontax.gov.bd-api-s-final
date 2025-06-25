<?php

namespace App\Models\Tenders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

}
