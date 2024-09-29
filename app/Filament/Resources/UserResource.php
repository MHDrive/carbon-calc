<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\UserRoleEnum;
use function Termwind\style;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Hash;
use App\Filament\Exports\UserExporter;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use App\Services\Classes\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\UserResource\RelationManagers;
use League\CommonMark\Extension\Attributes\Node\Attributes;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/global-resource.navigation.group.master_data');
    }

    // protected static ?string $pluralModelLabel = 'Pengguna';
    public static function getPluralModelLabel(): string
    {
        return __('filament-panels::resources/global-resource.plural-model-label.user-model.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Split::make([
                            Forms\Components\Section::make(static::getImageUserFormSchema()),
                            Forms\Components\Section::make('Ganti Sandi Akun')
                                ->schema(static::getPasswordFormSchema())->grow(false),
                        ])->from('2xl'),
                        Forms\Components\Section::make()
                            ->schema(static::getDetailsUserFormSchema())->columnSpan(2),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->header(view('filament.tables.header', [
                'heading' => 'Data Pengguna'
            ]))
            // ->headerActions([ hanya untuk testing
            //     ExportAction::make()
            //     ->label('Unduh CSV')
            //         ->exporter(UserExporter::class)
            // ])
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->getStateUsing(function ($record) {
                        return $record->first_name . ' ' . $record->last_name;
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                // ->before(function ($record) {
                //     $data = $record->purgeMeta();
                //     return $data;
                // }),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getImageUserFormSchema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('avatar')
                ->hiddenLabel()
                ->collection('avatars')
                ->alignCenter()
                ->circle(fn(String $operation) => $operation === 'create' ? true : false),
            Placeholder::make('first_name')
                ->hiddenLabel()
                ->content(
                    function ($record, $operation) {
                        if ($operation === 'create') {
                            return new HtmlString('');
                        }
                        return new HtmlString(
                            "<div style='text-align: center;'><strong style='font-size: 20px;'>"
                                . $record->first_name . " " . $record->last_name
                                . "</strong></div>"
                        );
                    }
                )

        ];
    }
    public static function getPasswordFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('password')
                ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->rule(Password::default())
                ->autocomplete('new-password')
                ->dehydrated(fn($state): bool => filled($state))
                ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                ->live(debounce: 500)
                ->same('passwordConfirmation'),
            Forms\Components\TextInput::make('passwordConfirmation')
                ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                ->password()
                ->revealable(filament()->arePasswordsRevealable())
                ->required()
                ->visible(fn(Get $get, String $operation): bool => $operation === 'create' ? true : filled($get('password')))
                ->dehydrated(false)
        ];
    }
    public static function getDetailsUserFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('first_name')
                ->label('Nama Pertama')
                ->minLength(3)
                ->maxLength(50)
                ->autofocus(),
            Forms\Components\TextInput::make('last_name')
                ->label('Nama Terakhir')
                ->minLength(3)
                ->maxLength(50),
            Forms\Components\TextInput::make('username')
                ->label('Username')
                ->minLength(3)
                ->maxLength(50),
            Forms\Components\ToggleButtons::make('roles')
                ->label('Role '),
            Forms\Components\TextInput::make('number_unique')
                ->label('NPM/NIP/NIK')
                ->minLength(10)
                ->maxLength(50),
            Forms\Components\Select::make('institutes')
                ->label('Instansi/Institusi')
                ->relationship('institutes', 'name')
                ->createOptionForm([
                    Forms\Components\Select::make('type')
                        ->label('Jenis')
                        ->options([
                            'institusi' => 'INSTANSI',
                            'instansi' => 'INSTITUSI',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label('Nama')
                        ->required(),
                    Forms\Components\TextInput::make('address')
                        ->label('Alamat')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('city')
                        ->label('Kota')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Nomor Telepon')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('description')
                        ->label('Deskripsi')
                        ->maxLength(255),
                ]),
            Forms\Components\TextInput::make('phone_number')
                ->label('Nomor HP')
                ->numeric()
                ->minLength(10)
                ->maxLength(100),
            Forms\Components\TextInput::make('email')
                ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
        ];
    }
}
