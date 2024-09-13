<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Forms;
use App\Livewire\TimelineSubmission;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;

class ViewSubmissionGuest extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Section::make()
                            ->heading('Status Pengajuan')
                            ->schema([
                                Forms\Components\Livewire::make(TimelineSubmission::class)
                            ]),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->extraAttributes(['class' => 'color-primary'])
                                    ->schema([
                                        Forms\Components\ViewField::make('description')
                                            ->view('filament.forms.components.description-submission'),
                                    ]),

                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make(),
                                        SpatieMediaLibraryFileUpload::make('file_submission')
                                            ->hiddenLabel()
                                            ->deletable(false)
                                            ->collection('file_submissions')
                                            ->hintAction(
                                                MediaAction::make('View')
                                                    ->button()
                                                    // ->iconButton()
                                                    ->icon('heroicon-o-eye')
                                                    ->media(fn() => ($this->getRecord()->getFirstMedia('file_submissions')->getUrl()))
                                            )
                                            ->alignCenter()
                                            ->columnSpan(1),
                                        Forms\Components\Group::make()
                                    ])
                                    ->columns(3)
                            ])
                    ])
                    ->operation('view')
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath())
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }
}
