<?php

namespace Database\Factories;

use App\Models\Vessel;
use Illuminate\Database\Eloquent\Factories\Factory;

class VesselFactory extends Factory
{
    protected $model = Vessel::class;

    public function definition()
    {
        return [
            'vessel_name' => $this->faker->word . ' ' . $this->faker->word,
            'job_no' => 'JOB' . $this->faker->unique()->numberBetween(100, 999),
            'port_name' => $this->faker->randomElement(['Port of Singapore', 'Port of Rotterdam', 'Port of Los Angeles', 'Port of Dubai']),
            'eta' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'etd' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => $this->faker->randomElement(['1', '2', '3']),
            'berth_no' => 'B' . $this->faker->numberBetween(1, 20),
            'voy' => 'V' . $this->faker->numberBetween(100, 999),
            'grt' => $this->faker->numberBetween(50000, 60000),
            'nrt' => $this->faker->numberBetween(40000, 50000),
            'dwt' => $this->faker->numberBetween(80000, 90000),
            'eosp' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'aado' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'nor_tendered' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'nor_accepted' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'dropped_anchor' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'heaved_up_anchor' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'pilot_boarded' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'first_line' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'berthed_on' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'made_fast' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'sailed_on' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'arrival_fuel_oil' => $this->faker->numberBetween(1000, 2000),
            'arrival_diesel_oil' => $this->faker->numberBetween(500, 1000),
            'arrival_fresh_water' => $this->faker->numberBetween(200, 500),
            'arrival_draft_fwd' => $this->faker->randomFloat(1, 10, 12),
            'arrival_draft_aft' => $this->faker->randomFloat(1, 10, 12),
            'departure_fuel_oil' => $this->faker->numberBetween(800, 1500),
            'departure_diesel_oil' => $this->faker->numberBetween(400, 800),
            'departure_fresh_water' => $this->faker->numberBetween(150, 300),
            'departure_draft_fwd' => $this->faker->randomFloat(1, 10, 12),
            'departure_draft_aft' => $this->faker->randomFloat(1, 10, 12),
            'next_port_of_call' => $this->faker->randomElement(['Port of Shanghai', 'Port of Hamburg', 'Port of Long Beach', 'Port of Mumbai']),
            'eta_next_port' => $this->faker->dateTimeBetween('+2 months', '+3 months'),
            'any_requirements' => $this->faker->randomElement(['No special requirements', 'Requires additional fuel', 'Requires fresh water supply']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}