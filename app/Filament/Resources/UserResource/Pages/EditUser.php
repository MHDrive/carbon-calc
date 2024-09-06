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
                ->before(function ($record) {
                    $data = $record->purgeMeta();
                    return $data;
                }),
        ];
    }

    // menangani proses update user
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['name'] = $data['first_name'] . ' ' . ($data['last_name'] ?? '');
        $record->update($data);
        if (data_get($data, 'meta')) {
            $record->setManyMeta($data['meta']);
        }

        return $record;
    }

    // mengatur set data form fill profile
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['meta'] = $this->getRecord()->getAllMeta()->toArray();
        return $data;
    }
}
