<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SensorReadingResource\Pages;
use App\Filament\Resources\SensorReadingResource\RelationManagers;
use App\Models\SensorReading;
use Filament\Forms;
use App\Models\Sensor;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SensorReadingsExport;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class SensorReadingResource extends Resource

{
    protected static ?string $model = SensorReading::class;

    protected static ?string $navigationIcon = 'heroicon-c-arrow-down-on-square';
    // protected static ?string $navigationLabel = 'Monitoring Data';
    protected static ?string $navigationGroup = 'Data Monitoring';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('sensor.name')->label('Sensor Name')->sortable()->searchable(),
            TextColumn::make('value')->label('Value')->sortable(),
            TextColumn::make('measurement_time')->label('Measurement Time')->sortable()->dateTime(),
            TextColumn::make('created_at')->label('Created At')->sortable()->dateTime(),
        ])
        ->filters([
            // Filter berdasarkan sensor
            Tables\Filters\SelectFilter::make('sensor_id')
                ->label('Sensor Name')
                ->relationship('sensor', 'name')
                ->options(Sensor::all()->pluck('name', 'id')->toArray()),
        ])
        ->headerActions([
            // Tambahkan tombol export di header actions (atas tabel)
            Action::make('export')
                ->label('Export Data to Excel')
                ->form([
                    Forms\Components\Select::make('sensor_id')
                        ->label('Filter by Sensor')
                        ->options(Sensor::all()->pluck('name', 'id')->toArray())
                        ->placeholder('All Sensors'),
                ])
                ->action(fn (array $data) => static::exportFilteredData($data))
                ->icon('heroicon-c-arrow-down-on-square'),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function exportFilteredData(array $data)
    {
        // Ambil semua data atau data berdasarkan sensor_id yang difilter
        $query = SensorReading::with('sensor');

        if (isset($data['sensor_id']) && !empty($data['sensor_id'])) {
            $query = $query->where('sensor_id', $data['sensor_id']);
        }

        // Batasi jumlah data yang diekspor
        $exportData = $query->limit(6000) // Batasi ke 1000 data
            ->get()
            ->map(function ($reading) {
                // Debugging untuk memastikan kolom value ada
                Log::info('Exporting reading', [
                    'sensor_name' => $reading->sensor->name,
                    'value' => $reading->value,
                    'measurement_time' => $reading->measurement_time,
                    // 'created_at' => $reading->created_at,
                ]);

                return [
                    'sensor_name' => $reading->sensor->name,
                    'value' => $reading->value,
                    'measurement_time' => $reading->measurement_time,
                    // 'created_at' => $reading->created_at,
                ];
            });

        // Ekspor data yang difilter dan dibatasi ke Excel
        return Excel::download(new SensorReadingsExport($exportData), 'filtered_sensor_readings.xlsx');
    }




    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSensorReadings::route('/'),
        ];
    }
}
