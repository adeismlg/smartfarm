<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Sensor;

class Monitor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; // Ikon di sidebar
    protected static string $view = 'filament.pages.monitor'; // Mengarah ke view custom

    public function getSensorsWithReadings()
    {
        // Ambil semua sensor beserta data sensor readings-nya
        return Sensor::with('readings')
                     ->get();
    }
}
