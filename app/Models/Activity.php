<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', // Description of the activity (watering, fertilizing, etc.)
        'farm_id', // Foreign key for the farm
    ];

    // Relasi dengan Farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}

