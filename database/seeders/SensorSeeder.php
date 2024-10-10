<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sensor;
use App\Models\Farm;

class SensorSeeder extends Seeder
{
    public function run()
    {
        $farm1 = Farm::where('name', 'Farm 1')->first();

        // Create Sensors for Farm 1
        Sensor::create([
            'name' => 'Temperature Sensor',
            'type' => 'temperature',
            'mqtt_topic' => 'farm1/sensors/temperature',
            'farm_id' => $farm1->id,
        ]);

        Sensor::create([
            'name' => 'Humidity Sensor',
            'type' => 'humidity',
            'mqtt_topic' => 'farm1/sensors/humidity',
            'farm_id' => $farm1->id,
        ]);
    }
}
