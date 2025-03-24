<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'description',
    ];

    // علاقة فئة الرسوم مع الرسوم الثابتة
    public function fixedFees()
    {
        return $this->hasMany(FixedFee::class);
    }
}
