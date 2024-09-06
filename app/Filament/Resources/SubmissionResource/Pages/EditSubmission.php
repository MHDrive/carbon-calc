<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Filament\Resources\SubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubmission extends EditRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // $user = $this->getRecord()->user; 
        // $media = $user->getFirstMedia('avatars'); 
        // dd($media);
        $data['meta'] = $this->getRecord()->user->getAllMeta()->toArray();
        // $data['media'] = $this->getRecord();
        return $data;
    }
}
