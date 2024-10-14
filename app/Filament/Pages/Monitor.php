<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Monitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.monitor';

    protected static ?string $navigationLabel = 'Monitoring Data';
    protected static ?string $navigationGroup = 'Data Monitoring';
    

    public $sensorData; // Variabel untuk menyimpan data sensor

    public function mount()
    {
        // Mengambil data sensor dan pembacaan terakhir dari database
        $this->sensorData = $this->getSensorsWithReadings();
    }

    // Method untuk mengambil data sensor dari database
    public function getSensorsWithReadings()
{
    // Ambil semua sensor beserta data pembacaannya
    $sensors = Sensor::with(['readings' => function ($query) {
        $query->orderBy('measurement_time', 'desc')->take(50); // Ambil 50 data berdasarkan waktu pengukuran
    }])->get();

    // Format data sensor
    $sensorData = [];
    foreach ($sensors as $sensor) {
        $sensorData[] = [
            'sensor' => $sensor->name,
            'readings' => $sensor->readings->map(function ($reading) {
                return [
                    'avg_value' => $reading->value,
                    'time_interval' => Carbon::parse($reading->measurement_time)->timestamp, // Gunakan measurement_time
                ];
            })
        ];
    }

    return $sensorData;
}

}
