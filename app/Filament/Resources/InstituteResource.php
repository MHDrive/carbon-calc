<?php

namespace App\Filament\Resources;

use App\Enum\StatusInstituteEnum;
use App\Filament\Resources\InstituteResource\Pages;
use App\Filament\Resources\InstituteResource\RelationManagers;
use App\Models\Institute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InstituteResource extends Resource
{
    protected static ?string $model = Institute::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/global-resource.navigation.group.master_data');
    }

    // protected static ?string $pluralModelLabel = 'Pengguna';
    public static function getPluralModelLabel(): string
    {
        return __('filament-panels::resources/global-resource.plural-model-label.institute-model.institutes');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Instansi / Institusi')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),                 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInstitutes::route('/'),
        ];
    }
}
