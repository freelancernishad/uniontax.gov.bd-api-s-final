<?php

namespace App\Models\Tenders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderFormBuy extends Model
{
    use HasFactory;


    protected $fillable=[
        'tender_id',
        'name',
        'PhoneNumber',
        'form_code',
        'status',
    ];

}
