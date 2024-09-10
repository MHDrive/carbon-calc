<?php

namespace App\Filament\Resources\RoleResource\Pages;

use Filament\Actions;
use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\PermissionRegistrar;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = app(PermissionRegistrar::class)
            ->getPermissions()
            ->mapWithKeys(fn ($permission) => [$permission->name => $this->getRecord()->hasPermissionTo($permission->name)])
            ->toArray();

        return $data;
    }
}
