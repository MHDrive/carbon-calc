<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource;

class InfoClimateWet extends ListRecords
{
    protected static string $resource = ClimateWetResource::class;

    protected static string $view = 'filament.clusters.calculations.resources.climate-wet-resource.pages.info-climate-wet';

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? 'Info';
    }
}
