<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Actuator;
use App\Models\Farm;

class ActuatorSeeder extends Seeder
{
    public function run()
    {
        $farm1 = Farm::where('name', 'Farm 1')->first();

        // Create Actuators for Farm 1
        Actuator::create([
            'name' => 'Water Pump',
            'type' => 'pump',
            'status' => false,
            'farm_id' => $farm1->id,
        ]);

        Actuator::create([
            'name' => 'Greenhouse Fan',
            'type' => 'fan',
            'status' => false,
            'farm_id' => $farm1->id,
        ]);
    }
}
