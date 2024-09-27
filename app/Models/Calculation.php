<?php

namespace App\Models;

use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use App\Models\Recapitulation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'climate_type',
        "formula_type",
        'provinces',
        'regencies',
        'districts',
        'villages',
        'forest_name',
        'surface_area',
        'surface_area_unit',
        'method',
        'meta',
        'total_biomass_tonPerHa',
        'total_carbon_stocks_tonPerHa',
        'total_biomass_kgPerHa',
        'total_carbon_stocks_kgPerHa',
    ];

    protected function casts(): array
    {
        return [
            'climate_type' => ClimateTypeEnum::class,
            'formula_type' => FormulaTypeEnum::class,
            'meta' => 'array'
        ];
    }
}
