<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
        'user_id', // Foreign key for the user that owns the farm
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan Asset
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    // Relasi dengan Sensor
    public function sensors()
    {
        return $this->hasMany(Sensor::class);
    }

    // Relasi dengan Actuator
    public function actuators()
    {
        return $this->hasMany(Actuator::class);
    }

    // Relasi dengan Activity
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}
