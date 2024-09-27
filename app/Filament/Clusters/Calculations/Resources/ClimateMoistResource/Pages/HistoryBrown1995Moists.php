<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;

use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Table;
use App\Enums\FormulaTypeEnum;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource;

class HistoryBrown1995Moists extends ListRecords
{
    protected static string $resource = ClimateMoistResource::class;

    public function getBreadcrumb(): ?string
    {
        return static::$breadcrumb ?? 'Riwayat';
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(fn (): Builder => static::getModel()::query()->where('formula_type', FormulaTypeEnum::BROWN1995))
        ->columns([
            Tables\Columns\TextColumn::make('formula_type')
                ->label('Rumus')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('forest_name')
            ->label('Nama Hutan')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('surface_area')
            ->label('Luas Area')
                ->getStateUsing(fn($record) => $record->surface_area . ' ' . $record->surface_area_unit)
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('method')
            ->label('Metode Hitung')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
            ->label('Tanggal Dibuat')
                ->dateTime('d F Y')
                ->sortable(),
        ])
        ->filters([
            //
        ])
        ->actions([
            Tables\Actions\ViewAction::make()->url(fn($record) => route('filament.admin.calculations.resources.climate-moists.view-result', ['record' => $record->id])),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }
}
