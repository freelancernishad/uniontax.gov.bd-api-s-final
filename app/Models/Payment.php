<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway',
        'transaction_id',
        'currency',
        'amount',
        'fee',
        'status',
        'response_data',
        'payment_method',
        'payer_email',
        'paid_at',
        'coupon_id',
        'payable_type',
        'payable_id',
        'union', // New column
        'trxId', // New column
        'sonodId', // New column
        'sonod_type', // New column
        'applicant_mobile', // New column
        'date', // New column
        'month', // New column
        'year', // New column
        'paymentUrl', // New column
        'ipnResponse', // New column
        'method', // New column
        'payment_type', // New column
        'balance', // New column
    ];

    protected $casts = [
        'response_data' => 'array', // Cast JSON data to an array
        'ipnResponse' => 'array', // Cast JSON data to an array
        'paid_at' => 'datetime', // Cast as a datetime
        'date' => 'datetime', // Cast as a datetime
    ];

    // Define relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Scope for completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope for payments with discounts.
     */
    public function scopeDiscounted($query)
    {
        return $query->whereNotNull('coupon_id');
    }

    /**
     * Scope for payments by gateway.
     */
    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    /**
     * Scope for payments by service or package.
     */
    public function scopeForPayable($query, $payableType, $payableId)
    {
        return $query->where('payable_type', $payableType)->where('payable_id', $payableId);
    }

    public function sonod()
    {
        // Defines a belongsTo relationship with the Sonod model using the sonodId as the foreign key
        return $this->belongsTo(Sonod::class, 'sonodId', 'id');
    }

    public function tax()
    {
        // Defines a belongsTo relationship with the HoldingBokeya model using sonodId as the foreign key
        return $this->belongsTo(HoldingBokeya::class, 'sonodId', 'id');
    }

    // protected $appends = ['sonods','holding_tax'];

    public function getHoldingTaxAttribute()
    {
        if ($this->sonod_type === 'holdingtax') {
            return $this->tax->holdingTax()->select('id', 'maliker_name', 'gramer_name', 'mobile_no', 'holding_no')->first();
        }

    }
    public function getSonodsAttribute()
    {
        if ($this->sonod_type != 'holdingtax') {
            return $this->sonod()->select('id', 'applicant_name', 'applicant_present_village', 'applicant_holding_tax_number', 'applicant_mobile')->first();
            return $this->sonod;
        }

    }

    public function tenderinvoice()
    {
        // Defines a belongsTo relationship with the TanderInvoice model using sonodId as the foreign key
        return $this->belongsTo(TanderInvoice::class, 'sonodId', 'id');
    }


}
