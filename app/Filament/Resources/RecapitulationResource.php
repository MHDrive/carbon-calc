<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecapitulationResource\Pages;
use App\Filament\Resources\RecapitulationResource\RelationManagers;
use App\Models\Recapitulation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RecapitulationResource extends Resource
{
    protected static ?string $model = Recapitulation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'id')
                    ->required(),
                Forms\Components\Select::make('calculation_id')
                    ->relationship('calculation', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('user.id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('calculation.name')
                    ->label('Nama Rumus')
                    ->sortable(),
                Tables\Columns\TextColumn::make('calculation.climate_type')
                    ->label('Iklim')
                    ->sortable(),
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
            'index' => Pages\ListRecapitulations::route('/'),
            'create' => Pages\CreateRecapitulation::route('/create'),
            'edit' => Pages\EditRecapitulation::route('/{record}/edit'),
        ];
    }
}
