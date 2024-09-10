<?php

namespace App\Filament\Resources;

use App\Enums\StatusSubmissionEnum;
use Filament\Forms;
use Filament\Infolists\Components;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Submission;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\SubmissionResource\RelationManagers;
use Filament\Forms\Components\ViewField;
use Filament\Infolists\Infolist;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->isAdmin()) {
            return true;
        }
        return false;
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-panels::resources/global-resource.plural-model-label.submission-model.submissions');
    }

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
            ->header(view('filament.tables.header', [
                'heading' => 'Data Pengajuan'
            ]))
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->getStateUsing(fn($record) => $record->user->first_name . ' ' . $record->user->last_name)
                    ->searchable(
                        true,
                        fn($query, $search) => $query
                            ->orWhereHas(
                                'user',
                                fn($q) => $q
                                    ->where('first_name', 'like', "%{$search}%")
                                    ->orWhere('last_name', 'like', "%{$search}%")
                            )
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => StatusSubmissionEnum::colorOptions()[$state] ?? 'defaultColor'),
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
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewSubmission::route('/{record}'),
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
                ->relationship(
                    name: 'user',
                    modifyQueryUsing: fn(Builder $query) => $query->orderBy('first_name')->orderBy('last_name'),
                )
                ->getOptionLabelFromRecordUsing(fn(Model $record) => "{$record->first_name} {$record->last_name}")
                // ->searchable(['first_name', 'last_name'])
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
                        ->label('Nama')
                        ->formatStateUsing(fn($state, $record) => ($record->getFilamentName()))
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->schema([]),
                Components\Section::make('Content')
                    ->schema([
                        Components\SpatieMediaLibraryImageEntry::make('file_submission')
                            ->collection('file_submissions')
                    ])
                    ->collapsible(),
            ]);
    }
}
