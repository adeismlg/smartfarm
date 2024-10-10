<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeasurementResource\Pages;
use App\Filament\Resources\MeasurementResource\RelationManagers;
use App\Models\Measurement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MeasurementResource extends Resource
{
    protected static ?string $model = Measurement::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->label('Measurement Type'),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->label('Value'),
                Forms\Components\Select::make('asset_id')
                    ->relationship('asset', 'name')
                    ->required()
                    ->label('Asset'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value'),
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Asset'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeasurements::route('/'),
            'create' => Pages\CreateMeasurement::route('/create'),
            'edit' => Pages\EditMeasurement::route('/{record}/edit'),
        ];
    }
}