<?php

namespace App\Filament\Widgets;

use Error;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use App\Enums\UserRoleEnum;
use App\Models\Calculation;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CustomChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.chart-widget';

    protected static ?string $heading = 'Cadangan Karbon';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $climate_type = $this->filters['climate_type'] ?? null;
        $formula_type = $this->filters['formula_type'] ?? null;
        $method = $this->filters['method'] ?? null;

        $data = Trend::query(Calculation::query()->when($climate_type, function ($query) use ($climate_type) {
            return $query->where('climate_type', $climate_type);
        })
            ->when($formula_type, function ($query) use ($formula_type) {
                return $query->where('formula_type', $formula_type);
            })
            ->when($method, function ($query) use ($method) {
                return $query->where('method', $method);
            }))
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('total_carbon_stocks_tonPerHa');

        $labels = $data->map(function (TrendValue $value) {
            return Carbon::parse($value->date)->translatedFormat('F'); // Mengubah ke nama bulan
        });

        return [
            'datasets' => [
                [
                    'label' => 'Blog posts',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
