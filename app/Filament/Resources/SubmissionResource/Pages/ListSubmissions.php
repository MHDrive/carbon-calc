<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Enums\UserRoleEnum;
use App\Filament\Resources\SubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubmissions extends ListRecords
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        if(auth()->user()->roles === UserRoleEnum::ADMIN->value){
            return [];
        }
        return [
            Actions\CreateAction::make()
        ];
    }
}
