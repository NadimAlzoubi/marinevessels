<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vessel;

class VesselSeeder extends Seeder
{
    public function run()
    {
        // إنشاء 20 سجلات وهمية
        Vessel::factory()->count(20)->create();
    }
}