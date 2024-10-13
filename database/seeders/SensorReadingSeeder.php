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
        $interval = 12; // Data setiap 12 detik (untuk 5 data per menit)

        // Indeks untuk mengelola urutan sensor
        $sensorIndex = 0;

        $currentDate = $startDate->copy();

        // Loop hingga akhir periode data yang didefinisikan
        while ($currentDate->lessThanOrEqualTo($endDate)) {
            // Mengambil sensor berdasarkan urutan index yang bergantian
            $sensor = $sensors[$sensorIndex % $sensors->count()];

            // Menghasilkan data dummy berdasarkan jenis sensor dan waktu
            $value = $this->generateSensorValue($sensor->type, $currentDate);

            // Membuat record untuk pembacaan sensor dengan menambahkan 'measurement_time'
            SensorReading::create([
                'sensor_id' => $sensor->id,
                'value' => $value,
                'measurement_time' => $currentDate, // Waktu pengukuran
                'created_at' => $currentDate,
                'updated_at' => $currentDate,
            ]);

            // Pindah ke sensor berikutnya dalam urutan
            $sensorIndex++;

            // Tambahkan interval waktu (12 detik)
            $currentDate->addSeconds($interval);
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
            case 'amonia': // Mengganti dari ammonia menjadi amonia
                return $this->generateAmoniaValue($currentDate);
            case 'wind speed':
                return $this->generateWindSpeedValue($currentDate);
            default:
                return 0; // Default value jika tipe sensor tidak dikenal
        }
    }

    /**
     * Generate nilai suhu (temperature) dengan pola harian dan musiman yang lebih natural
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateTemperatureValue($currentDate)
    {
        $month = $currentDate->month;
        $hour = $currentDate->hour;

        // Pola musiman suhu, lebih halus dengan variasi kecil per bulan
        $baseTemp = 25; // Suhu dasar
        if ($month >= 6 && $month <= 8) {
            // Musim dingin: lebih rendah di kisaran 22°C - 26°C
            $baseTemp = 24 + sin(deg2rad(($month - 6) * 30)) * 1.5; // Smoothing musiman
        } elseif ($month >= 9 && $month <= 11) {
            // Musim panas: lebih tinggi di kisaran 28°C - 32°C
            $baseTemp = 30 + sin(deg2rad(($month - 9) * 30)) * 1.5;
        } else {
            // Musim hangat: bulan lainnya
            $baseTemp = 26 + sin(deg2rad(($month - 3) * 30)) * 1.5;
        }

        // Pengaruh waktu hari, lebih halus pada malam hari
        if ($hour < 6 || $hour > 18) {
            $baseTemp -= 1.5; // Suhu turun pada malam hari secara halus
        }

        return round($baseTemp + rand(-0.5, 0.5), 1); // Variasi kecil untuk membuat lebih natural
    }

    /**
     * Generate nilai kelembaban (humidity) dengan pola harian dan musiman yang lebih landai
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateHumidityValue($currentDate)
    {
        $hour = $currentDate->hour;
        $month = $currentDate->month;

        // Faktor musiman untuk kelembapan (lebih lembap di musim hujan)
        $seasonMultiplier = ($month >= 6 && $month <= 8) ? 0.85 : 1.15; // Musim hujan atau panas

        // Pola harian lebih halus untuk kelembapan
        if ($hour >= 6 && $hour <= 10) {
            // Pagi hari: 65% - 85%
            return round(rand(650, 850) / 10 * $seasonMultiplier, 1);
        } elseif ($hour >= 11 && $hour <= 16) {
            // Siang hari: 50% - 65%
            return round(rand(500, 650) / 10 * $seasonMultiplier, 1);
        } else {
            // Malam hari: 60% - 80%
            return round(rand(600, 800) / 10 * $seasonMultiplier, 1);
        }
    }

    /**
     * Generate nilai lux (cahaya) dengan pola siang dan malam yang tajam
     *
     * @param Carbon $currentDate
     * @return int
     */
    private function generateLuxValue($currentDate)
    {
        $hour = $currentDate->hour;

        // Cahaya meningkat tajam pada siang hari
        if ($hour >= 6 && $hour <= 18) {
            // Siang hari: antara 1500 - 3000 lux
            return rand(1500, 3000);
        } else {
            // Malam hari: sangat rendah atau nol
            return rand(0, 10);
        }
    }

    /**
     * Generate nilai amonia (ammonia) yang lebih lambat dan halus
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateAmoniaValue($currentDate)
    {
        // Menghasilkan tren lambat dengan variasi yang lebih halus
        $baseAmonia = 10; // Nilai dasar
        $variation = sin(deg2rad($currentDate->dayOfYear)) * 1.5; // Smoothing pola lambat sepanjang tahun
        return round($baseAmonia + $variation + rand(-0.5, 0.5), 1); // Variasi lebih kecil
    }

    /**
     * Generate nilai kecepatan angin (wind speed) dengan pola musiman yang lebih landai
     *
     * @param Carbon $currentDate
     * @return float
     */
    private function generateWindSpeedValue($currentDate)
    {
        $month = $currentDate->month;

        // Pola musiman yang lebih halus untuk kecepatan angin
        if ($month >= 6 && $month <= 8) {
            // Musim angin kencang: lebih tinggi antara 13.0 - 18.0 kph
            return round(13 + sin(deg2rad(($month - 6) * 30)) * 2.5, 1);
        } elseif ($month >= 9 && $month <= 11) {
            // Musim tenang: lebih rendah antara 10.0 - 14.0 kph
            return round(10 + sin(deg2rad(($month - 9) * 30)) * 2.5, 1);
        } else {
            // Bulan lainnya: antara 8.0 - 12.0 kph
            return round(8 + sin(deg2rad(($month - 3) * 30)) * 2.5, 1);
        }
    }
}
