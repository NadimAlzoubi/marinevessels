<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_name',
        'description',
        'amount',
        'tax_rate',
        'fee_category_id',
    ];

    // علاقة الرسوم الثابتة بفئة الرسوم
    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    // علاقة كثير إلى كثير مع الفواتير عبر جدول invoice_fees
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_fees', 'fixed_fee_id', 'invoice_id')
            ->withPivot('quantity', 'discount')
            ->withTimestamps();
    }
}