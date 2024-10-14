<?php

namespace App\Filament\Resources\SensorReadingResource\Pages;

use App\Filament\Resources\SensorReadingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSensorReading extends ViewRecord
{
    protected static string $resource = SensorReadingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
