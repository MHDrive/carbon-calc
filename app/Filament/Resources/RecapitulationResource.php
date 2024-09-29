<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use App\Models\Recapitulation;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\RecapitulationExporter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecapitulationResource\Pages;
use App\Filament\Resources\RecapitulationResource\RelationManagers;

class RecapitulationResource extends Resource
{
    protected static ?string $model = Recapitulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Actions\Action::make('Details'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'create' => Pages\CreateRecapitulation::route('/create'),
            'edit' => Pages\EditRecapitulation::route('/{record}/edit'),
        ];
    }
}
