<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActuatorResource\Pages;
use App\Filament\Resources\ActuatorResource\RelationManagers;
use App\Models\Actuator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActuatorResource extends Resource
{
    protected static ?string $model = Actuator::class;
    protected static ?string $navigationIcon = 'heroicon-m-light-bulb';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Actuator Name'),
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->label('Type'),
                Forms\Components\Toggle::make('status')
                    ->label('Status'),
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
                    ->label('Actuator Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Type'),
                // Mengganti BooleanColumn dengan IconColumn
                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean(), // Menggunakan boolean untuk menampilkan ikon centang/silang
                Tables\Columns\TextColumn::make('farm.name')
                    ->label('Farm'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActuators::route('/'),
            'create' => Pages\CreateActuator::route('/create'),
            'edit' => Pages\EditActuator::route('/{record}/edit'),
        ];
    }
}