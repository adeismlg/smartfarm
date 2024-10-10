<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Measurement;
use App\Models\Asset;

class MeasurementSeeder extends Seeder
{
    public function run()
    {
        $cornfield = Asset::where('name', 'Cornfield')->first();

        // Create Measurements for Cornfield
        Measurement::create([
            'type' => 'height',
            'value' => 1.5, // in meters
            'asset_id' => $cornfield->id,
        ]);

        Measurement::create([
            'type' => 'weight',
            'value' => 500, // in kg
            'asset_id' => $cornfield->id,
        ]);
    }
}
