<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Filament\Resources\SubmissionResource\RelationManagers;
use App\Models\Submission;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // protected static ?string $pluralModelLabel = 'verifikasi pengajuan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema(static::getMediaFormSchema()),
                        Forms\Components\Section::make()
                            ->schema(static::getMessageFormSchema()),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make()
                    ->schema(static::getDetailFormSchema())
                    ->columnSpan(2),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListSubmissions::route('/'),
            'create' => Pages\CreateSubmission::route('/create'),
            'edit' => Pages\EditSubmission::route('/{record}/edit'),
        ];
    }

    public static function getMediaFormSchema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('file_submission')
                ->label(fn(String $operation) => $operation === 'create' ? 'Upload File' : '')
                ->hiddenLabel(fn(String $operation) => $operation === 'create' ? false : true)
                ->collection('file_submissions'),
        ];
    }
    public static function getMessageFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('notes')
                ->required(),
        ];
    }
    public static function getUserFormSchema(): array
    {
        return [
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required(),
        ];
    }
    public static function getDetailFormSchema(): array
    {
        return [
            Forms\Components\Group::make()
                ->relationship('user')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Customer')
                        // ->formatStateUsing(fn($state, $record) => dd($record))
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->label('Email address')
                        ->email()
                        ->required(),
                    Forms\Components\TextInput::make('number_unique')
                        ->label('NPM/NIP/NIK')
                        ->required(),
                    Forms\Components\Select::make('institute')
                        ->label('Instansi/Institusi')
                        ->relationship('institutes', 'name')
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('No HP')
                        ->required(),
                ]),
            Forms\Components\TextInput::make('media.created_at')
                ->label('Tanggal Upload')
        ];
    }
}
