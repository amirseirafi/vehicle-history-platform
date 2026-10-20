<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vin',
        'chassis_number',
        'brand',
        'model',
        'trim',
        'manufacture_year_gregorian',
        'manufacture_year_jalali',
        'color',
        'engine_number',
        'plate_number',
        'current_mileage',
    ];

    protected function casts(): array
    {
        return [
            'manufacture_year_gregorian' => 'integer',
            'manufacture_year_jalali' => 'integer',
            'current_mileage' => 'integer',
        ];
    }
}