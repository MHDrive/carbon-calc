<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateWetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClimateWets extends ListRecords
{
    protected static string $resource = ClimateWetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
