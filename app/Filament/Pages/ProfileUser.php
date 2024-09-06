<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Placeholder;
use Filament\Pages\Auth\EditProfile as BaseProfile;
use App\Services\Classes\SpatieMediaLibraryFileUpload;

class ProfileUser extends BaseProfile
{

    // Membuat Form input untuk profile 
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('avatar')
                                            ->hiddenLabel()
                                            ->collection('avatars')
                                            ->circle(),
                                        Placeholder::make('name')
                                            ->hiddenLabel()
                                            ->content(fn($state) => new HtmlString("<div style='text-align: center;'><strong style='font-size: 27px;'>" . $state . "</strong></div>"))
                                    ]),
                                Forms\Components\Section::make("Ganti Sandi")
                                    ->schema([
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
                                            ->visible(fn(Get $get): bool => filled($get('password')))
                                            ->dehydrated(false)
                                    ]),
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->minLength(3)
                                    ->maxLength(50)
                                    ->autofocus(),
                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->minLength(3)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('username')
                                    ->label('Username')
                                    ->minLength(3)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('meta.number_unique')
                                    ->label('NPM/NIP/NIK')
                                    ->minLength(10)
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('meta.agency')
                                    ->label('Instansi/Institusi')
                                    ->minLength(10)
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('meta.telephone')
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
                            ])
                            ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data')
                // ->inlineLabel(! static::isSimple()),
            ),
        ];
    }

    // Menangani Proses Update Profile
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['name'] = $data['first_name'] . ' ' . ($data['last_name'] ?? '');
        $record->update($data);
        if (data_get($data, 'meta')) {
            $record->setManyMeta($data['meta']);
        }

        return $record;
    }

    // mengatur data form fill profile
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['meta'] = $this->getUser()->getAllMeta()->toArray();
        return $data;
    }
}
