<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'fixed_fee_id',
        'quantity',
        'amount',
        'tax_rate',
        'discount',
    ];

    // علاقة سجل الرسوم مع الفاتورة
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    // علاقة سجل الرسوم مع الرسم الثابت
    public function fixedFee()
    {
        return $this->belongsTo(FixedFee::class);
    }
}
