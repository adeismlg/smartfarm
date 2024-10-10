<?php

namespace App\Filament\Resources\ActuatorResource\Pages;

use App\Filament\Resources\ActuatorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActuator extends EditRecord
{
    protected static string $resource = ActuatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
