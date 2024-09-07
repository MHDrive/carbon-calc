<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    // button delete halaman edit
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                // ->before(function ($record) {
                //     $data = $record->purgeMeta();
                //     return $data;
                // }),
        ];
    }

    // menangani proses update user
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }
}
