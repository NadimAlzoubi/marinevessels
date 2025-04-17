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
        'description', 
        'quantity',
        'amount',
        'tax_rate',
        'discount',
        'pricing_method',
        'pricing_context',
    ];

    protected $casts = [
        'pricing_context' => 'json',
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
