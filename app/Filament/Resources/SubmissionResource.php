<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Submission;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components;
use App\Enums\StatusSubmissionEnum;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\ViewField;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\SubmissionResource\RelationManagers;
use Filament\Notifications\Notification;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

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
                            ->schema(static::getMediaFormSchema())->disabled(),
                        Forms\Components\Section::make()
                            ->schema(static::getMessageFormSchema()),
                    ])
                    ->columnSpan(1),
                Forms\Components\Section::make()
                    ->schema(static::getDetailFormSchema())
                    ->disabled()
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
                Tables\Columns\TextColumn::make('user.number_unique')
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->label('NPM/NIP/NIK'),
                Tables\Columns\TextColumn::make('institute.name')
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Instansi/Institusi'),
                Tables\Columns\TextColumn::make('user.phone_number')
                    // ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Nomor HP'),
                Tables\Columns\TextColumn::make('media.created_at')
                    ->date()
                    ->label('Tanggal Pengajuan'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('is_active')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Status Berlaku')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->recordUrl(false)
            ->actions([
                // Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make()->label('Verifikasi')->color('info')->hidden(fn ($record) =>  $record->status === StatusSubmissionEnum::APPROVED || $record->status === StatusSubmissionEnum::REJECTED)
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('approved')
                //     ->label('Terima')
                //     ->color('success')
                //     ->icon('heroicon-s-check-circle')
                //     ->action(function ($record) {
                //         $update = $record->update(['status' => StatusSubmissionEnum::APPROVED->value]);
                //         return $update;
                //     }),
                // Tables\Actions\Action::make('rejected')
                //     ->label('Tolak')
                //     ->color('danger')
                //     ->icon('heroicon-s-x-circle')
                //     ->action(function ($record) {
                //         $update = $record->update(['status' => StatusSubmissionEnum::REJECTED->value]);
                //         return $update;
                //     }),
                // ]),
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
                ->hiddenLabel()
                ->deletable(false)
                ->collection('file_submissions')
                ->hintAction(
                    MediaAction::make('View')
                        ->button()
                        // ->iconButton()
                        ->icon('heroicon-o-eye')
                        ->media(fn($operation) => $operation === 'create' ?: ($this->getRecord()->getFirstMedia('file_submissions')->getUrl()))
                )
        ];
    }
    public static function getMessageFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('notes')
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
}
