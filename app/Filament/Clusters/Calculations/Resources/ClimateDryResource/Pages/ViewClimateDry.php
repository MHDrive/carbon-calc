<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use Filament\Tables;
use Filament\Actions;
use Plank\Metable\Meta;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;

class ViewClimateDry extends ViewRecord
// implements HasTable
{
    // use InteractsWithTable;
    protected static string $resource = ClimateDryResource::class;

    protected static ?string $title = 'Hasil Kalkulator';

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'hasil';
    }
    
    protected function makeInfolist(): Infolist
    {
        return parent::makeInfolist()
            ->schema([
                Section::make()
                    ->schema([
                        ViewEntry::make('info-result')
                        ->hiddenLabel()
                            ->viewData([
                                'data' => $this->getRecord(),
                            ])
                            ->view('filament.infolists.components.info-result-calculation'),
                    ])
            ])
            ->record($this->getRecord())
            ->columns($this->hasInlineLabels() ? 1 : 2)
            ->inlineLabel($this->hasInlineLabels());
    }

    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->query(fn() => dd(Meta::where('metable_type', static::getModel())->where('metable_id', $this->getRecord()->id)->get()))
    //         ->columns([
    //             Tables\Columns\TextColumn::make('name')
    //                 ->searchable()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('forest_name')
    //                 ->searchable()
    //                 ->sortable(),

    //             Tables\Columns\TextColumn::make('plot')
    //                 ->label('Plot')
    //                 ->sortable(),

    //             Tables\Columns\TextColumn::make('plot_area')
    //                 ->label('Plot Area')
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('forest_name')
    //                 ->searchable()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('created_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }
}
