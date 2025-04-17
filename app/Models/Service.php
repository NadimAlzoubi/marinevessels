<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['code', 'name', 'description', 'active'];
    
    public function tariffCategories(): HasMany
    {
        return $this->hasMany(TariffCategory::class);
    }
}
