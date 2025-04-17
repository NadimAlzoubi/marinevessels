<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'fax',
        'email',
        'website',
        'trn',
        'notes',
        'contact_person',
        'contact_person_phone',
        'status',
        'country',
        'type',
    ];

    /**
     * Get the vessels owned by the client.
     */
    public function vessels()
    {
        return $this->hasMany(Vessel::class);
    }
}
