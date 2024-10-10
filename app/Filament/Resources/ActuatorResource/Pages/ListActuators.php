<?php

namespace App\Filament\Resources\ActuatorResource\Pages;

use App\Filament\Resources\ActuatorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListActuators extends ListRecords
{
    protected static string $resource = ActuatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
