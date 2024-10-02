<?php

namespace App\Filament\Widgets;

use App\Models\Calculation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Cadangan Karbon', '192.1k'),
            Stat::make('Pohon Yang Dihitung', '21%'),
            Stat::make('Lokasi Yang Dihitung', '3:12')
        ];
    }
}
