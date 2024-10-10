<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\Farm;

class AssetSeeder extends Seeder
{
    public function run()
    {
        $farm1 = Farm::where('name', 'Farm 1')->first();

        // Create Assets for Farm 1
        Asset::create([
            'name' => 'Tractor',
            'description' => 'A powerful tractor for farming',
            'type' => 'equipment',
            'farm_id' => $farm1->id,
        ]);

        Asset::create([
            'name' => 'Cornfield',
            'description' => 'A large cornfield',
            'type' => 'plant',
            'farm_id' => $farm1->id,
        ]);
    }
}