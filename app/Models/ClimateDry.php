<?php

namespace App\Models;

use Plank\Metable\Meta;
use Plank\Metable\Metable;
use App\Enums\ClimateTypeEnum;
use App\Models\Calculation as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClimateDry extends Model
{
    use HasFactory, Metable;

    protected $table = 'calculations';

    protected static function boot()
    {
        parent::boot();

        // Membatasi query hanya untuk `climate_type` = 'dry'
        static::addGlobalScope('climate_type', function ($builder) {
            $builder->where('climate_type', ClimateTypeEnum::DRY);
        });
    }

//     public function meta()
// {
//     return $this->morphMany(Meta::class, 'metable');
// }
}
