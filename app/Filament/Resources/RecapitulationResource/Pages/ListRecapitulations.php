<?php

namespace App\Filament\Resources\RecapitulationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Exports\Models\Export;
use App\Filament\Exports\RecapitulationExporter;
use App\Filament\Resources\RecapitulationResource;

class ListRecapitulations extends ListRecords
{
    protected static string $resource = RecapitulationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->exporter(RecapitulationExporter::class)
                ->fileName(fn (Export $export): string => "Rekap-{$export->getKey()}"),
        ];
    }
}
