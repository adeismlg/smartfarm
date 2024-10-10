<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type', // Type of asset (plant, equipment, etc.)
        'farm_id', // Foreign key for the farm
    ];

    // Relasi dengan Farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    // Relasi dengan Measurement
    public function measurements()
    {
        return $this->hasMany(Measurement::class);
    }
}
