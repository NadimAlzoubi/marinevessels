<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_name',
        'description',
        'amount',
        'pricing_rule',
        'fee_category_id',
        'tariff_category_id',
    ];

    // أضف العلاقة مع TariffCategory
    public function tariffCategory(): BelongsTo
    {
        return $this->belongsTo(TariffCategory::class);
    }

    // علاقة الرسوم الثابتة بفئة الرسوم
    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class, 'fee_category_id');
    }

    // علاقة كثير إلى كثير مع الفواتير عبر جدول invoice_fees
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_fees', 'fixed_fee_id', 'invoice_id')
            ->withPivot('quantity', 'discount')
            ->withTimestamps();
    }
}
