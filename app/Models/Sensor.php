<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // Type of sensor (temperature, humidity, etc.)
        'mqtt_topic', // MQTT topic for the sensor
        'farm_id', // Foreign key for the farm
    ];

    // Relasi dengan Farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function readings()
    {
        return $this->hasMany(SensorReading::class);
    }
}
