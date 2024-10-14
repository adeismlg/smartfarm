<?php

namespace App\Filament\Resources\SensorReadingResource\Pages;

use App\Filament\Resources\SensorReadingResource;
use Filament\Resources\Pages\ListRecords;

class ListSensorReadings extends ListRecords
{
    protected static string $resource = SensorReadingResource::class;

    // Nonaktifkan tombol create, edit, dan delete
    protected function getActions(): array
    {
        return [];
    }
}
