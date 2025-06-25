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
}
