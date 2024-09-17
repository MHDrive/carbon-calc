<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class InfoClimateDries extends ListRecords
{
    protected static string $resource = ClimateDryResource::class;

    protected static string $view = 'filament.clusters.calculations.resources.climate-dry-resource.pages.info-climate-dry';

    public $dryRecord;
    public function mount(): void
    {
        $this->dryRecord = static::getResource()::getEloquentQuery()->where('climate_type', 'dry')->get();
        $this->authorizeAccess();
        $this->loadDefaultActiveTab();
    }
}
