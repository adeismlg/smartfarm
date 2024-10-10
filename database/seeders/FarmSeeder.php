<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FarmSeeder extends Seeder
{
    public function run()
    {
        // Cek apakah pengguna dengan email farmer@example.com ada
        $farmer = User::firstOrCreate(
            ['email' => 'farmer@example.com'],
            [
                'name' => 'Farmer User',
                'password' => Hash::make('password'), // Pastikan password di-hash
            ]
        );

        // Jika pengguna farmer berhasil dibuat atau ditemukan, buat farm
        if ($farmer) {
            Farm::create([
                'name' => 'Farm 1',
                'description' => 'This is Farm 1',
                'location' => 'Location 1',
                'user_id' => $farmer->id,
            ]);

            Farm::create([
                'name' => 'Farm 2',
                'description' => 'This is Farm 2',
                'location' => 'Location 2',
                'user_id' => $farmer->id,
            ]);
        }
    }
}
