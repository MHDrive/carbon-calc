<?php

namespace App\Models;

use App\Enums\ClimateTypeEnum;
use App\Models\Calculation as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClimateMoist extends Model
{
    use HasFactory;

    protected $table = 'calculations';

    protected static function boot()
    {
        parent::boot();

        // Membatasi query hanya untuk `climate_type` = 'dry'
        static::addGlobalScope('climate_type', function ($builder) {
            $builder->where('climate_type', ClimateTypeEnum::MOIST);
        });
    }
}
