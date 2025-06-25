<?php

namespace App\Models\Tenders;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanderInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanderid',
        'amount',
        'khat',
        'orthobochor',
        'status',
        'date',
        'year',
        'union_name'
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function tenderList()
    {
        return $this->belongsTo(TenderList::class,'tanderid','id');
    }
}
