<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SensorReading;
use App\Models\Sensor;
use Carbon\Carbon;

class SensorReadingSeeder extends Seeder
{
    public function run()
    {
        // Mendefinisikan parameter data
        $sensors = Sensor::orderBy('id', 'asc')->get(); // Mengambil sensor berurutan berdasarkan ID
        $startDate = Carbon::create(2024, 6, 1, 7, 0, 0); // Mulai dari 1 Juni 2024 pukul 07:00 pagi
        $endDate = $startDate->copy()->addDays(40); // Data selama 40 hari ke depan
        $interval = 10 * 60; // Data setiap 10 menit (dalam detik)

        $currentDate = $startDate->copy();

        // Loop hingga akhir periode data yang didefinisikan
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            // Menghitung umur ayam dalam hari
            $ageInDays = $currentDate->diffInDays($startDate);

            // Loop untuk setiap sensor dan mengirimkan data secara bersamaan
            foreach ($sensors as $sensor) {
                // Menghasilkan data dummy berdasarkan jenis sensor dan umur ayam
                $value = $this->generateSensorValue($sensor->type, $currentDate, $ageInDays);

                // Membuat record untuk pembacaan sensor dengan menambahkan 'measurement_time'
                SensorReading::create([
                    'sensor_id' => $sensor->id,
                    'value' => $value,
                    'measurement_time' => $currentDate, // Waktu pengukuran yang sama untuk semua sensor
                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ]);
            }

            // Tambahkan interval waktu (10 menit) sebelum pengiriman data berikutnya
            $currentDate->addSeconds($interval);
        }
    }

    /**
     * Generate nilai sensor berdasarkan tipe sensor, waktu, dan umur ayam
     *
     * @param string $sensorType
     * @param Carbon $currentDate
     * @param int $ageInDays
     * @return float
     */
    private function generateSensorValue($sensorType, $currentDate, $ageInDays)
    {
        switch (strtolower($sensorType)) {
            case 'temperature':
                return $this->generateTemperatureValue($currentDate, $ageInDays);
            case 'humidity':
                return $this->generateHumidityValue($currentDate);
            case 'lux':
                return $this->generateLuxValue($currentDate);
            case 'amonia':
                return $this->generateAmoniaValue($currentDate, $ageInDays);
            case 'wind speed':
                return $this->generateWindSpeedValue($currentDate);
            default:
                return 0; // Default value jika tipe sensor tidak dikenal
        }
    }

    /**
     * Generate nilai suhu (temperature) dengan rentang 24°C - 29°C yang bergantung pada umur ayam
     *
     * @param Carbon $currentDate
     * @param int $ageInDays
     * @return float
     */
    private function generateTemperatureValue($currentDate, $ageInDays)
    {
        $hour = $currentDate->hour;
        $minute = $currentDate->minute;

        // Rentang suhu yang bergantung pada umur ayam
        $minTemp = 24; // Suhu terendah
        $maxTemp = 29; // Suhu tertinggi

        // Perubahan suhu berdasarkan umur ayam (semakin tinggi seiring bertambahnya usia)
        $tempByAge = $minTemp + ($maxTemp - $minTemp) * ($ageInDays / 40); // Linear increase by age

        // Pola suhu harian (naik di siang hari, turun di malam hari)
        $dailyVariation = sin(deg2rad(($hour * 60 + $minute) * (180 / (24 * 60)))) * 1.5;

        return round($tempByAge + $dailyVariation + rand(-0.2, 0.2), 1); // Variasi kecil untuk membuatnya lebih natural
    }

    /**
     * Generate nilai kelembaban (humidity) dengan rentang 92% - 100%
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateHumidityValue($currentDate)
    {
        $hour = $currentDate->hour;
        $minute = $currentDate->minute;

        // Rentang kelembaban antara 92% dan 100%
        $minHumidity = 92;
        $maxHumidity = 100;

        // Pola gradual naik turun kelembapan
        $dailyVariation = sin(deg2rad(($hour * 60 + $minute) * (180 / (24 * 60)))) * 4;
        $baseHumidity = ($maxHumidity + $minHumidity) / 2 + $dailyVariation;

        return round($baseHumidity + rand(-0.5, 0.5), 1); // Variasi kecil untuk membuatnya lebih natural
    }

    /**
     * Generate nilai lux (cahaya) dengan pola siang dan malam yang lebih realistis
     *
     * @param Carbon $currentDate
     * @return int
     */
    private function generateLuxValue($currentDate)
    {
        $hour = $currentDate->hour;

        // Pola siang dan malam untuk cahaya, dengan kandang tetap menyala di malam hari
        if ($hour >= 6 && $hour <= 17) {
            // Siang hari: antara 1500 - 3000 lux
            return rand(1500, 3000);
        } else {
            // Malam hari: lampu menyala, sekitar 200 - 500 lux
            return rand(200, 500);
        }
    }

    /**
     * Generate nilai amonia (ammonia) yang meningkat seiring bertambahnya umur ayam
     *
     * @param Carbon $currentDate
     * @param int $ageInDays
     * @return float
     */
    private function generateAmoniaValue($currentDate, $ageInDays)
    {
        // Amonia meningkat seiring bertambahnya umur ayam (5 ppm di hari awal hingga 20 ppm di hari panen)
        $minAmonia = 5; // Amonia terendah di awal
        $maxAmonia = 20; // Amonia tertinggi di akhir periode

        // Perubahan amonia berdasarkan umur ayam
        $amoniaByAge = $minAmonia + ($maxAmonia - $minAmonia) * ($ageInDays / 40); // Linear increase by age

        // Variasi harian kecil pada nilai amonia
        $dailyVariation = rand(-0.5, 0.5);

        return round($amoniaByAge + $dailyVariation, 1); // Variasi kecil untuk natural effect
    }

    /**
     * Generate nilai kecepatan angin (wind speed) dengan rentang 16 mph - 19 mph
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateWindSpeedValue($currentDate)
    {
        $hour = $currentDate->hour;
        $minute = $currentDate->minute;

        // Rentang kecepatan angin antara 16 mph dan 19 mph
        $minWindSpeed = 16;
        $maxWindSpeed = 19;

        // Pola gradual naik turun kecepatan angin
        $dailyVariation = sin(deg2rad(($hour * 60 + $minute) * (180 / (24 * 60)))) * 1.5;
        $baseWindSpeed = ($maxWindSpeed + $minWindSpeed) / 2 + $dailyVariation;

        return round($baseWindSpeed + rand(-0.2, 0.2), 1); // Variasi kecil untuk membuatnya lebih natural
    }
}
