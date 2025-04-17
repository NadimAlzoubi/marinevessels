<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_type',
        'call_type',
        'vessel_id',
        'invoice_date',
        'currency',
        'sub_total',
        'tax_total',
        'grand_total',
    ];

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }
    
    public function fees()
    {
        return $this->belongsToMany(FixedFee::class, 'invoice_fees', 'invoice_id', 'fixed_fee_id')
            ->withPivot('quantity', 'amount', 'tax_rate', 'discount', 'description')
            ->withTimestamps()->with('feeCategory'); 

    }


    public function invoiceFees()
    {
        return $this->hasMany(InvoiceFee::class);
    }
}
