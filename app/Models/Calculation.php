<?php

namespace App\Models;

use App\Enums\ClimateTypeEnum;
use App\Models\Recapitulation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calculation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'climate_type',
        'description',
        'province',
        'regency',
        'subdistrict',
        'village',
        'forest_name',
        'surface_area',
        'total_plot',
        'meta',
        'total_biomass',
        'total_carbon_stocks',
    ];

    protected function casts(): array
    {
        return [
            'climate_type' => ClimateTypeEnum::class,
            'meta' => 'array'
        ];
    }

    public function recapitulation(): HasMany
    {
        return $this->hasMany(Recapitulation::class);
    }
}
