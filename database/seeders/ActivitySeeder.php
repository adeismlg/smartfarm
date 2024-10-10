<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Farm;

class ActivitySeeder extends Seeder
{
    public function run()
    {
        $farm1 = Farm::where('name', 'Farm 1')->first();

        // Create Activities for Farm 1
        Activity::create([
            'description' => 'Watering the cornfield',
            'farm_id' => $farm1->id,
        ]);

        Activity::create([
            'description' => 'Fertilizing the cornfield',
            'farm_id' => $farm1->id,
        ]);
    }
}