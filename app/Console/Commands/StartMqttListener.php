<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\Facades\MQTT;
use App\Models\Sensor; // Import model Sensor
use App\Models\SensorReading; // Import model SensorReading

class StartMqttListener extends Command
{
    protected $signature = 'mqtt:listen';
    protected $description = 'Mendengarkan data dari perangkat IoT melalui MQTT';

    public function handle()
    {
        $this->info('Memulai mendengarkan data dari perangkat IoT...');

        // Mendapatkan koneksi ke broker MQTT
        $mqtt = MQTT::connection();

        // Ambil semua sensor dari database
        $sensors = Sensor::all();

        // Berlangganan ke topik untuk setiap sensor
        foreach ($sensors as $sensor) {
            $mqtt->subscribe($sensor->mqtt_topic, function (string $topic, string $message) use ($sensor) {
                $this->info("Data diterima di topik {$topic}: {$message}");

                // Simpan data ke database
                SensorReading::create([
                    'sensor_id' => $sensor->id, // Menyimpan ID sensor
                    'value' => (float)$message // Menyimpan nilai sensor
                ]);
            }, 0);
        }

        // Loop untuk mendengarkan pesan
        while (true) {
            // Memeriksa status koneksi
            if (!$mqtt->isConnected()) {
                $this->info('Koneksi terputus. Mencoba menyambung kembali...');
                $this->reconnect($mqtt);
            }
            $mqtt->loop(); // Menjaga koneksi aktif
        }
    }

    private function reconnect($mqtt)
    {
        // Coba menyambung kembali ke broker
        while (!$mqtt->connected()) {
            $this->info('Menghubungkan kembali ke broker MQTT...');
            if ($mqtt->connect(env('MQTT_CLIENT_ID'), env('MQTT_AUTH_USERNAME'), env('MQTT_AUTH_PASSWORD'))) {
                $this->info('Terhubung kembali ke broker MQTT!');
            } else {
                $this->error('Gagal terhubung, mencoba lagi dalam 5 detik...');
                sleep(5); // Tunggu 5 detik sebelum mencoba lagi
            }
        }
    }
}
