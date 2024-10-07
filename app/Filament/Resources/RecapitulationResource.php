<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use App\Models\Recapitulation;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RecapitulationExport;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Exports\RecapitulationExporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecapitulationResource\Pages;
use App\Filament\Resources\RecapitulationResource\RelationManagers;

class RecapitulationResource extends Resource
{
    protected static ?string $model = Recapitulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $pluralModelLabel = 'Rekapitulasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('climate_type'),
                Forms\Components\TextInput::make('formula_type')
                    ->maxLength(255),
                Forms\Components\TextInput::make('provinces')
                    ->maxLength(255),
                Forms\Components\TextInput::make('regencies')
                    ->maxLength(255),
                Forms\Components\TextInput::make('districts')
                    ->maxLength(255),
                Forms\Components\TextInput::make('villages')
                    ->maxLength(255),
                Forms\Components\TextInput::make('forest_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('surface_area')
                    ->maxLength(255),
                Forms\Components\TextInput::make('surface_area_unit')
                    ->maxLength(255),
                Forms\Components\TextInput::make('method'),
                Forms\Components\TextInput::make('meta'),
                Forms\Components\TextInput::make('total_biomass_tonPerHa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_carbon_stocks_tonPerHa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_biomass_kgPerHa')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_carbon_stocks_kgPerHa')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Hasil Perhitungan Cadangan Carbon')
            ->query(fn(): Builder => static::getModel()::query()->where('user_id', Auth::user()->id))
            ->groups([
                Group::make('climate_type')
                    ->label('Iklim'),
                Group::make('formula_type')
                    ->label('Rumus'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('climate_type')
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->sortable()
                    ->label('Iklim'),
                Tables\Columns\TextColumn::make('formula_type')
                    ->label('Rumus')
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('forest_name')
                    ->label('Nama Hutan')
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('surface_area')
                    ->label('Luas Area')

                    ->getStateUsing(function ($record) {
                        if ($record->surface_area_unit === 'm²') {
                            return round(($record->surface_area / 10000), 2) . ' ha';
                        }
                        if ($record->surface_area_unit === 'km²') {
                            return round(($record->surface_area / 100), 2) . ' ha';
                        }
                        if ($record->surface_area_unit === 'ha') {
                            return $record->surface_area . ' ha';
                        }
                    })
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('method')
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->sortable()
                    ->label('Metode Hitung'),
                Tables\Columns\TextColumn::make('total_biomass_tonPerHa')
                    ->label('Jumlah Biomassa (Ton/Ha)')
                    ->extraHeaderAttributes([
                        'class' => 'w-40 !whitespace-normal change',
                    ])
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make(),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_carbon_stocks_tonPerHa')
                    ->label('Jumlah Cadangan Carbon (Ton/Ha)')
                    ->extraHeaderAttributes([
                        'class' => 'w-40 !whitespace-normal change',
                    ])
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make(),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_biomass_kgPerHa')
                    ->label('Jumlah Biomassa (Kg/Ha)')
                    ->extraHeaderAttributes([
                        'class' => 'w-40 !whitespace-normal change',
                    ])
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make(),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_carbon_stocks_kgPerHa')
                    ->label('Jumlah Cadangan Carbon (Kg/Ha)')
                    ->extraHeaderAttributes([
                        'class' => 'w-40 !whitespace-normal change',
                    ])
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make(),
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->extraHeaderAttributes([
                        'class' => '!whitespace-normal change',
                    ])
                    ->date('d F Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('formula_type')
                    ->options(FormulaTypeEnum::class),
                SelectFilter::make('method')
                    ->options([
                        'sensus' => 'sensus',
                        'sampling' => 'sampling',
                    ])
            ])
            ->actions([
                Tables\Actions\Action::make('Details')
                    ->url(fn($record) => route('filament.admin.resources.recapitulations.view', ['record' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                    ->icon('heroicon-o-user')
                    ->color('success')
                    ->form([
                        Forms\Components\Radio::make('check')
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
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Checkbox::make('no')
                                    ->default(true)
                                    ->label('No'),
                                Forms\Components\Checkbox::make('id')
                                    ->label('ID'),
                                Forms\Components\Checkbox::make('climate_type')
                                    ->label('Iklim'),
                                Forms\Components\Checkbox::make('formula_type')
                                    ->label('Rumus'),
                                Forms\Components\Checkbox::make('address')
                                    ->live(debounce: 500)
                                    ->label('Alamat'),
                                    Forms\Components\Fieldset::make('Alamat')
                                    ->hidden(fn(Get $get) => !$get('address'))
                                    ->schema([
                                        Forms\Components\Radio::make('condition_address')
                                            ->hiddenLabel()
                                            ->default('satuCell')
                                            ->options([
                                                'satuCell' => 'Satu Cell',
                                                'perCell' => 'Per Cell',
                                            ]),
                                    ])->columnSpan(1),
                                Forms\Components\Checkbox::make('forest_name')
                                    ->label('Nama Hutan'),
                                Forms\Components\Checkbox::make('surface_area')
                                    ->label('Luas Area'),
                                Forms\Components\Checkbox::make('method')
                                    ->label('Metode'),
                                Forms\Components\Checkbox::make('meta')
                                    ->label('Data Meta'),
                                Forms\Components\Checkbox::make('total_biomass_tonPerHa')
                                    ->label('Total Biomass Ton/Ha'),
                                Forms\Components\Checkbox::make('total_carbon_stocks_tonPerHa')
                                    ->label('Total Cadangan Carbon Ton/Ha'),
                                Forms\Components\Checkbox::make('total_biomass_kgPerHa')
                                    ->label('Total Biomass Kg/Ha'),
                                Forms\Components\Checkbox::make('total_carbon_stocks_kgPerHa')
                                    ->label('Total Cadangan Carbon Ton/Ha'),
                            ])
                    ])
                    ->modalWidth(MaxWidth::Small)
                    ->action(fn (Collection $records, array $data) => Excel::download(new RecapitulationExport($data, $records), 'invoices.csv', \Maatwebsite\Excel\Excel::XLSX))
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecapitulations::route('/'),
            'view' => Pages\viewRecapitulation::route('/{record}/view'),
        ];
    }
}
