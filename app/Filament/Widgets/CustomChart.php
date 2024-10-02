<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\Calculation;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class CustomChart extends ChartWidget
{
    protected static ?string $heading = 'Cadangan Karbon';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Trend::model(Calculation::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
 
    return [
        'datasets' => [
            [
                'label' => 'Blog posts',
                'data' => $data->map(fn (TrendValue $value) => dd($value)),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
