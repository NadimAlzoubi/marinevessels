<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TariffCategory extends Model
{
    protected $fillable = ['service_id', 'code', 'name', 'description', 'unit_of_measurement', 'active'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    
    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }
}
