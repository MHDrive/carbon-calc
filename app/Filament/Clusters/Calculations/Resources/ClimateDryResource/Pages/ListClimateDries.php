<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClimateDries extends ListRecords
{
    protected static string $resource = ClimateDryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
