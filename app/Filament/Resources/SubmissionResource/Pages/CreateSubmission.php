<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubmissionResource;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
            ->schema($this->getSteps())
            ->columns(null);
    }

    public function getSteps(): array
    {
        return [
            Section::make()
                ->description('Silahkan isi untuk menambahkan pengajuan baru')
                ->schema([
                    Group::make()
                        ->schema(SubmissionResource::getMediaFormSchema()),
                    Group::make()
                        ->schema(SubmissionResource::getUserFormSchema()),
                ])
        ];
    }
}
