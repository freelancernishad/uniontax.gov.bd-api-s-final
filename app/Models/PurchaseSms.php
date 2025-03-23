<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseSms extends Model
{
    use HasFactory;

    protected $table = 'purchase_sms';

    protected $fillable = [
        'union_name',
        'mobile',
        'trx_id',
        'bank_trx_id',
        'method',
        'amount',
        'sms_amount',
        'payment_status',
        'status',
    ];

    // Define any relationships if necessary, for example:
    // public function user() {
    //     return $this->belongsTo(User::class);
    // }
}
