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
    ];

    protected $table = 'sensor_readings';

    protected $guarded = [];

    // Relasi dengan Sensor
    public function sensor()
    {
        return $this->belongsTo(Sensor::class);
    }
}
