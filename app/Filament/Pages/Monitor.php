<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Sensor;
use App\Models\SensorReading;
use Illuminate\Http\Request;
use Carbon\Carbon;

class Monitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; // Ikon di sidebar
    protected static string $view = 'filament.pages.monitor'; // Mengarah ke view custom

    public $sensorData; // Mendefinisikan variabel untuk disiapkan ke view

    public function mount()
    {
        // Ambil semua data sensor beserta pembacaannya untuk penggunaan di view
        $this->sensorData = $this->getSensorsWithReadings();
    }

    public function getSensorsWithReadings()
    {
        // Ambil semua sensor beserta data sensor readings-nya
        $sensors = Sensor::with(['readings' => function($query) {
            // Mengambil pembacaan terakhir dari setiap sensor (Real-time data)
            $query->orderBy('created_at', 'desc')->take(50);
        }])->get();

        $sensorData = [];
        foreach ($sensors as $sensor) {
            $sensorData[] = [
                'sensor' => $sensor->name,
                'readings' => $sensor->readings->map(function ($reading) {
                    return [
                        'avg_value' => $reading->value,
                        'time_interval' => Carbon::parse($reading->created_at)->timestamp,
                    ];
                })
            ];
        }

        return $sensorData;
    }

    /**
     * Method untuk menangani permintaan data history.
     * Mengembalikan data pembacaan sensor berdasarkan rentang tanggal dan interval yang dipilih.
     */
    public function getData(Request $request)
    {
        $startDate = Carbon::parse($request->input('startDate'))->startOfDay();
        $endDate = Carbon::parse($request->input('endDate'))->endOfDay();
        $interval = (int)$request->input('interval', 30); // Default interval: 30 detik

        $sensors = Sensor::all();
        $sensorData = [];

        foreach ($sensors as $sensor) {
            // Mengambil data pembacaan sensor dalam rentang tanggal yang dipilih
            $readings = SensorReading::where('sensor_id', $sensor->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at')
                ->get();

            // Mengelompokkan data pembacaan berdasarkan interval
            $groupedReadings = $readings->groupBy(function ($reading) use ($interval) {
                return floor(Carbon::parse($reading->created_at)->timestamp / ($interval * 60));
            })->map(function ($group) {
                return [
                    'avg_value' => $group->avg('value'),
                    'time_interval' => $group->first()->created_at->timestamp,
                ];
            })->values();

            $sensorData[] = [
                'sensor' => $sensor->name,
                'readings' => $groupedReadings
            ];
        }

        return response()->json($sensorData);
    }
}
