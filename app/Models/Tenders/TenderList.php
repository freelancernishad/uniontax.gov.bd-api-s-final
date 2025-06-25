<?php

namespace App\Models\Tenders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenderList extends Model
{
    use HasFactory;


    protected $fillable=[
       'union_name',
       'tender_id',
       'tender_type',
       'memorial_no',
       'tender_name',
       'description',
       'tender_word_no',
       'division',
       'district',
       'thana',
       'union',
       'govt_price',
       'form_price',
       'deposit_percent',
       'noticeDate',
       'form_buy_last_date',
       'tender_start',
       'tender_end',
       'tender_open',
       'tender_roles',
       'status',
       'committe1name',
       'committe1position',
       'commette1phone',
       'commette1pass',
       'committe2name',
       'committe2position',
       'commette2phone',
       'commette2pass',
       'committe3name',
       'committe3position',
       'commette3phone',
       'commette3pass',
       'bankName',
       'bankCheck',
       'daysOfDepositeAmount',
       'permitDetials',
    ];


    public function tanderInvoices()
    {
        return $this->hasMany(TanderInvoice::class);
    }

}
