<?php

namespace App\Filament\Resources\RecapitulationResource\Pages;

use App\Filament\Resources\RecapitulationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRecapitulations extends ListRecords
{
    protected static string $resource = RecapitulationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
