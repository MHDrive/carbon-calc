<?php

namespace App\Filament\Exports;

use stdClass;
use App\Models\Recapitulation;
use Filament\Actions\Exports\Exporter;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class RecapitulationExporter extends Exporter
{
    // protected static ?string $model = Recapitulation::class;

    public static function getColumns(): array
    {
        // Header utama
        $columns = [
            ExportColumn::make('no')
                ->label('No')
                ->getStateUsing(function ($record) {
                    static $index = 0;
                    $index++;
                    return (string) $index;
                }),
            ExportColumn::make('climate_type')
                ->label('Iklim')
                ->state(fn($record) => $record->climate_type->getlabel()),
            ExportColumn::make('formula_type')
                ->label('Rumus')
                ->state(fn($record) => $record->formula_type->getlabel()),
            ExportColumn::make('method')
                ->label('Metode')
                ->formatStateUsing(fn($state) => ucwords($state)),
            ExportColumn::make('meta')
                ->label('Meta')
                ->listAsJson(),
            // ExportColumn::make('plot')
            //     ->getStateUsing(fn($record) => $record->meta['plot'] ?? '-'),
            // ExportColumn::make('Luas Plot')
            //     ->getStateUsing(fn($record) => $record->meta['plot_area'] ?? '-'),
            // Kolom untuk total
            ExportColumn::make('total_biomass_tonPerHa')
                ->label('Total Biomass (Ton/Ha'),
            ExportColumn::make('total_carbon_stocks_tonPerHa')
                ->label('Total carbon (Ton/Ha)'),
            ExportColumn::make('total_biomass_kgPerHa')
                ->label('Total biomass (Kg/Ha)'),
            ExportColumn::make('total_carbon_stocks_kgPerHa')
                ->label('Total carbon (Kg/Ha)'),
        ];
        return $columns;
    }


    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your recapitulation export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
