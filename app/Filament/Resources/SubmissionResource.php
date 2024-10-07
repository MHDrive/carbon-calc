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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SubmissionResource\Pages;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\SubmissionResource\RelationManagers;
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
        return 'Verifikasi Pengajuan';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->heading('Status Pengajuan')
                    ->schema([
                        Forms\Components\View::make('livewire.timeline-submission')
                    ]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->extraAttributes(['class' => 'color-primary'])
                            ->schema([
                                ViewField::make('description')
                                    ->view('filament.forms.components.description-submission'),
                            ]),

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\Hidden::make('user_id')
                                        ->default(Auth::id())
                                ]),
                                SpatieMediaLibraryFileUpload::make('file_submission')
                                    ->label('Upload File')
                                    // ->hiddenLabel()
                                    ->collection('file_submissions'),
                                Forms\Components\Group::make()
                            ])
                            ->columns(3)
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Data Pengajuan')
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
                Tables\Actions\ViewAction::make()->label(fn($record) => ($record->status === StatusSubmissionEnum::APPROVED || $record->status === StatusSubmissionEnum::REJECTED) ? 'Detail' : 'Verifikasi')->color('info')
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
            'view' => Pages\ViewSubmission::route('/{record}'),
            'view-guest' => Pages\ViewSubmissionGuest::route('/{record}/view'),
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
            Forms\Components\DatePicker::make('media.created_at')
                ->label('Tanggal Upload')
                ->formatStateUsing(function ($record) {
                    $mediaItem = $record->media->first();

                    if ($mediaItem && $mediaItem->created_at) {
                        // Pastikan mengembalikan format yang valid untuk DatePicker
                        return $mediaItem->created_at->format('Y-m-d');
                    }

                    return null; // Atur null jika tidak ada data tanggal
                })
                ->displayFormat('d F Y') // Contoh: "07 October 2024"
                ->timezone('Asia/Jakarta')


        ];
    }
}
