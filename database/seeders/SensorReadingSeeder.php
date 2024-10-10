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
        $interval = 1; // Data setiap 10 menit

        // Indeks untuk mengelola urutan sensor
        $sensorIndex = 0;

        $currentDate = $startDate->copy();

        // Loop hingga akhir periode data yang didefinisikan
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            // Mengambil sensor berdasarkan urutan index yang bergantian
            $sensor = $sensors[$sensorIndex % $sensors->count()];

            // Menghasilkan data dummy berdasarkan jenis sensor dan waktu
            $value = $this->generateSensorValue($sensor->type, $currentDate);

            // Membuat record untuk pembacaan sensor
            SensorReading::create([
                'sensor_id' => $sensor->id,
                'value' => $value,
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ]);

            // Pindah ke sensor berikutnya dalam urutan
            $sensorIndex++;

            // Tambahkan interval waktu (10 menit)
            $currentDate->addMinutes($interval);
        }
    }

    /**
     * Generate nilai sensor berdasarkan tipe sensor dan waktu tertentu
     *
     * @param string $sensorType
     * @param Carbon $currentDate
     * @return float
     */
    private function generateSensorValue($sensorType, $currentDate)
    {
        switch (strtolower($sensorType)) {
            case 'temperature':
                return $this->generateTemperatureValue($currentDate);
            case 'humidity':
                return $this->generateHumidityValue($currentDate);
            case 'lux':
                return $this->generateLuxValue($currentDate);
            case 'amonia':
                return rand(0, 200) / 10; // Amonia antara 0.0 - 20.0 ppm
            case 'wind speed':
                return $this->generateWindSpeedValue($currentDate);
            default:
                return 0; // Default value jika tipe sensor tidak dikenal
        }
    }

    /**
     * Generate nilai suhu (temperature) dengan pola harian dan musiman
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateTemperatureValue($currentDate)
    {
        $month = $currentDate->month;
        $hour = $currentDate->hour;

        // Menggunakan pola musiman (bulan) untuk nilai suhu
        $baseTemp = 25; // Default temperature
        if ($month >= 6 && $month <= 8) {
            // Musim dingin: Juni - Agustus
            $baseTemp = 23 + rand(-2, 2); // antara 21°C - 25°C
        } elseif ($month >= 9 && $month <= 11) {
            // Musim panas: September - November
            $baseTemp = 30 + rand(-2, 3); // antara 28°C - 33°C
        } else {
            // Musim hangat: bulan lainnya
            $baseTemp = 25 + rand(-1, 3); // antara 24°C - 28°C
        }

        // Modifikasi berdasarkan waktu hari (lebih dingin di malam hari)
        if ($hour < 6 || $hour > 18) {
            $baseTemp -= rand(1, 3); // Mengurangi suhu di malam hari
        }

        return round($baseTemp, 1);
    }

    /**
     * Generate nilai kelembaban (humidity) dengan pola harian
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateHumidityValue($currentDate)
    {
        $hour = $currentDate->hour;

        // Mengatur kelembaban lebih tinggi di pagi hari dan menurun pada siang hari
        if ($hour >= 6 && $hour <= 10) {
            // Pagi hari: 70% - 90%
            return rand(700, 900) / 10;
        } elseif ($hour >= 11 && $hour <= 16) {
            // Siang hari: 50% - 70%
            return rand(500, 700) / 10;
        } else {
            // Malam hari: 60% - 80%
            return rand(600, 800) / 10;
        }
    }

    /**
     * Generate nilai lux (cahaya) berdasarkan waktu hari
     *
     * @param Carbon $currentDate
     * @return int
     */
    private function generateLuxValue($currentDate)
    {
        $hour = $currentDate->hour;

        if ($hour >= 6 && $hour <= 18) {
            // Siang hari: antara 1000 - 3000 lux (tergantung intensitas)
            return rand(1000, 3000);
        } else {
            // Malam hari: 0 - 10 lux (cahaya bulan)
            return rand(0, 10);
        }
    }

    /**
     * Generate nilai kecepatan angin (wind speed) dengan pola musiman
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateWindSpeedValue($currentDate)
    {
        $month = $currentDate->month;

        if ($month >= 6 && $month <= 8) {
            // Musim angin kencang: Juni - Agustus
            return rand(150, 200) / 10; // antara 15.0 - 20.0 kph
        } elseif ($month >= 9 && $month <= 11) {
            // Musim tenang: September - November
            return rand(100, 150) / 10; // antara 10.0 - 15.0 kph
        } else {
            // Musim biasa: bulan lainnya
            return rand(80, 150) / 10; // antara 8.0 - 15.0 kph
        }
    }
}
