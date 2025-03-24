<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_type', // يمكن أن يكون 'proforma' أو 'final'
        'vessel_id',
        'invoice_date',
        'sub_total',
        'tax_total',
        'grand_total',
    ];

    // علاقة الفاتورة بالسفينة
    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    // علاقة كثير إلى كثير مع الرسوم الثابتة عبر جدول invoice_fees
    public function fixedFees()
    {
        return $this->belongsToMany(FixedFee::class, 'invoice_fees')
                    ->withPivot('quantity', 'discount')
                    ->withTimestamps();
    }
}
