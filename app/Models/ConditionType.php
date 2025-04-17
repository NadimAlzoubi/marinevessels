<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConditionType extends Model
{
    protected $fillable = ['code', 'name', 'data_type', 'operator_type', 'available_operators', 'active'];
    
    protected $casts = [
        'available_operators' => 'json',
    ];
}
