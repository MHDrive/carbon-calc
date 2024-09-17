<?php

namespace App\Filament\Clusters\Calculations\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\ClimateWet;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Clusters\Calculations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\RelationManagers;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\EditClimateWet;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\ListClimateWets;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\CreateClimateWet;

class ClimateWetResource extends Resource
{
    protected static ?string $model = ClimateWet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Calculations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('climate_type'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('province')
                    ->maxLength(255),
                Forms\Components\TextInput::make('regency')
                    ->maxLength(255),
                Forms\Components\TextInput::make('subdistrict')
                    ->maxLength(255),
                Forms\Components\TextInput::make('village')
                    ->maxLength(255),
                Forms\Components\TextInput::make('forest_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('surface_area')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_plot')
                    ->maxLength(255),
                Forms\Components\TextInput::make('meta'),
                Forms\Components\TextInput::make('total_biomass')
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_carbon_stocks')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('climate_type'),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subdistrict')
                    ->searchable(),
                Tables\Columns\TextColumn::make('village')
                    ->searchable(),
                Tables\Columns\TextColumn::make('forest_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('surface_area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_plot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_biomass')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_carbon_stocks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => ListClimateWets::route('/'),
            'create' => CreateClimateWet::route('/create'),
            'edit' => EditClimateWet::route('/{record}/edit'),
        ];
    }
}
