<?php

namespace App\Filament\Widgets;

use App\Models\Calculation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        $climate_type = $this->filters['climate_type'] ?? null;
        $formula_type = $this->filters['formula_type'] ?? null;
        $method = $this->filters['method'] ?? null;

        $record = Calculation::where('climate_type', $climate_type)->orwhere('formula_type', $formula_type)->orwhere('method', $method)->get();
        $carbon_ton = 0;
        $totalNamaPohonYangDihitung = 0;
        foreach ($record as $item) {
            $carbon_ton += $item->total_carbon_stocks_tonPerHa;
            foreach ($item->meta as $meta) {
                foreach ($meta['input_calc'] as $input) {
                    if (!empty($input['tree_name'])) {
                        $totalNamaPohonYangDihitung++;
                    }
                }
            }
        }

        return [
            Stat::make('Cadangan Karbon', $carbon_ton . 'Ton/Ha'),
            Stat::make('Pohon Yang Dihitung', $totalNamaPohonYangDihitung),
            Stat::make('Lokasi Yang Dihitung', $record->count())
        ];
    }
}
