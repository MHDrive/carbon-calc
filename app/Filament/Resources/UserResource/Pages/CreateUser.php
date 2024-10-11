<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Buat Pengguna';

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getSteps())
            ->columns(null);
    }

    public function getSteps(): array
    {
        return [
            Section::make('Silahkan isi form untuk menambahkan data user baru')
                ->schema([
                    Split::make([
                        Section::make([
                            Group::make()
                                ->schema(UserResource::getDetailsUserFormSchema()),
                            Group::make()
                                ->schema(UserResource::getPasswordFormSchema()),
                        ]),
                        Section::make([
                            Group::make()
                                ->schema(UserResource::getImageUserFormSchema()),
                        ])->grow(false),
                    ])->from('md')


                ])
        ];
    }

    // menangani proses create user
    // protected function handleRecordCreation(array $data): Model
    // {
    //     $data['name'] = $data['first_name'] . ' ' . ($data['last_name'] ?? '');
    //     $record = new ($this->getModel())($data);
    //     if (data_get($data, 'meta')) {
    //         $record->setManyMeta($data['meta']);
    //     }
    //     $record->save();

    //     return $record;
    // }
}
