<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actuator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // Type of actuator (pump, light, etc.)
        'status', // Status of the actuator (on/off)
        'farm_id', // Foreign key for the farm
    ];

    // Relasi dengan Farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}

