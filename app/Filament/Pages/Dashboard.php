<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Actions\Action;
use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Notifications\Events\DatabaseNotificationsSent;

class Dashboard extends BaseDashboard
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // protected static string $view = 'filament.pages.dashboard';

    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('climate_type')
                            // ->native(false)
                            ->label('Iklim')
                            ->live(debounce:500)
                            ->afterStateUpdated(function ($state, Set $set) {
                                $set('formula_type', null);
                                $set('method', null);
                            })
                            ->options(ClimateTypeEnum::class),
                        Select::make('formula_type')
                            // ->native(false)
                            ->label('Rumus')
                            ->disabled(fn (Get $get) => !$get('climate_type'))
                            ->options(FormulaTypeEnum::class),
                        Select::make('method')
                            // ->native(false)
                            ->label('Metode')
                            ->disabled(fn (Get $get) => !$get('climate_type'))
                            ->options([
                                'sensus' => 'Sensus',
                                'sampling' => 'Sampling',
                            ]),
                    ])
                    ->columns(3),
            ]);
    }
}
