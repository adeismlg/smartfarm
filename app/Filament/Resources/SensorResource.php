<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorResource\Pages;
use App\Filament\Resources\SensorResource\RelationManagers;
use App\Models\Sensor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SensorResource extends Resource
{
    protected static ?string $model = Sensor::class;
    protected static ?string $navigationIcon = 'heroicon-c-rss';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Sensor Name'),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->label('Type'),
                Forms\Components\TextInput::make('mqtt_topic')
                    ->required()
                    ->label('MQTT Topic'),
                Forms\Components\Select::make('farm_id')
                    ->relationship('farm', 'name')
                    ->required()
                    ->label('Farm'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Sensor Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type'),
                Tables\Columns\TextColumn::make('mqtt_topic')
                    ->label('MQTT Topic'),
                Tables\Columns\TextColumn::make('farm.name')
                    ->label('Farm'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSensors::route('/'),
            'create' => Pages\CreateSensor::route('/create'),
            'edit' => Pages\EditSensor::route('/{record}/edit'),
        ];
    }
}