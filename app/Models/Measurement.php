<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // Type of measurement (height, weight, etc.)
        'value', // Value of the measurement
        'asset_id', // Foreign key for the asset
    ];

    // Relasi dengan Asset
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
