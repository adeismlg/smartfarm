<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorReading extends Model
{
    use HasFactory;

    protected $fillable = [
        'sensor_id', // ID Sensor yang terkait
        'value',     // Nilai pembacaan sensor
        'measurement_time'
    ];

    protected $table = 'sensor_readings';

    protected $guarded = [];

    protected $casts = [
        'measurement_time' => 'datetime', // Cast measurement_time sebagai datetime
    ];

    // Relasi dengan Sensor
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
