<?php

namespace App\Filament\Resources\RecapitulationResource\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Calculation;
use App\Models\Recapitulation;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecapitulationExport;
use Filament\Actions as ActionsExport;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Placeholder;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Exports\RecapitulationExporter;
use App\Filament\Resources\RecapitulationResource;

class ListRecapitulations extends ListRecords
{
    protected static string $resource = RecapitulationResource::class;

    protected function getActions(): array
    {
        return [
            ActionsExport\Action::make('Export')
                ->form([
                    Radio::make('check')
                        ->label('Centang Semua?')
                        ->boolean()
                        ->inline()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, $set) {
                            if($state){
                                // dd('oke');
                                $set('no', true);
                                $set('id', true);
                                $set('climate_type', true);
                                $set('formula_type', true);
                                $set('address', true);
                                $set('forest_name', true);
                                $set('surface_area', true);
                                $set('method', true);
                                $set('meta', true);
                                $set('total_biomass_tonPerHa', true);
                                $set('total_carbon_stocks_tonPerHa', true);
                                $set('total_biomass_kgPerHa', true);
                                $set('total_carbon_stocks_kgPerHa', true);
                            }else{
                                $set('no', null);
                                $set('id', null);
                                $set('climate_type', null);
                                $set('formula_type', null);
                                $set('address', null);
                                $set('forest_name', null);
                                $set('surface_area', null);
                                $set('method', null);
                                $set('meta', null);
                                $set('total_biomass_tonPerHa', null);
                                $set('total_carbon_stocks_tonPerHa', null);
                                $set('total_biomass_kgPerHa', null);
                                $set('total_carbon_stocks_kgPerHa', null);
                            }
                        })
                        ->inlineLabel(false),
                    Section::make()
                        ->schema([
                            Checkbox::make('no')
                                ->default(true)
                                ->label('No'),
                            Checkbox::make('id')
                                ->label('ID'),
                            Checkbox::make('climate_type')
                                ->label('Iklim'),
                            Checkbox::make('formula_type')
                                ->label('Rumus'),
                            Checkbox::make('address')
                                ->live(debounce: 500)
                                ->label('Alamat'),
                            Fieldset::make('Alamat')
                                ->hidden(fn(Get $get) => !$get('address'))
                                ->schema([
                                    Radio::make('condition_address')
                                        ->hiddenLabel()
                                        ->default('satuCell')
                                        ->options([
                                            'satuCell' => 'Satu Cell',
                                            'perCell' => 'Per Cell',
                                        ]),
                                ])->columnSpan(1),
                            Checkbox::make('forest_name')
                                ->label('Nama Hutan'),
                            Checkbox::make('surface_area')
                                ->label('Luas Area'),
                            Checkbox::make('method')
                                ->label('Metode'),
                            Checkbox::make('meta')
                                ->label('Data Meta'),
                            Checkbox::make('total_biomass_tonPerHa')
                                ->label('Total Biomass Ton/Ha'),
                            Checkbox::make('total_carbon_stocks_tonPerHa')
                                ->label('Total Cadangan Carbon Ton/Ha'),
                            Checkbox::make('total_biomass_kgPerHa')
                                ->label('Total Biomass Kg/Ha'),
                            Checkbox::make('total_carbon_stocks_kgPerHa')
                                ->label('Total Cadangan Carbon Ton/Ha'),
                        ])
                ])
                ->modalWidth(MaxWidth::Small)
                ->action(fn(array $data) => Excel::download(new RecapitulationExport($data, Recapitulation::where('user_id', Auth::user()->id)->get()), 'invoices.csv', \Maatwebsite\Excel\Excel::XLSX))
        ];
    }
}
