<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class InfoClimateMoist extends ListRecords
{
    protected static string $resource = ClimateMoistResource::class;

    protected static string $view = 'filament.clusters.calculations.resources.climate-moist-resource.pages.info-climate-moist';

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? 'Info';
    }
}
