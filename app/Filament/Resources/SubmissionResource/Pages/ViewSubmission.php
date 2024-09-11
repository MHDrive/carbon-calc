<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Enums\UserRoleEnum;
// use Filament\Actions;
use Illuminate\Support\HtmlString;
use App\Enums\StatusSubmissionEnum;
use Filament\Forms\Components\Grid;
use App\Livewire\TimelineSubmission;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected function getForms(): array
    {
        if (auth()->user()->roles === UserRoleEnum::ADMIN->value) {
            return [
                'form' => $this->form(
                    $this->makeForm()
                        ->schema([
                            Group::make()
                                ->schema([
                                    Section::make()
                                        ->schema(static::getResource()::getMediaFormSchema())->disabled(),
                                    Section::make()
                                        ->schema(static::getResource()::getMessageFormSchema()),
                                ])
                                ->columnSpan(1),
                            Section::make()
                                ->schema([
                                    Group::make()->schema(static::getResource()::getDetailFormSchema()),
                                    Group::make([
                                        Actions::make([
                                            Action::make('approve')
                                                ->label('Terima')
                                                ->color('success')
                                                ->icon('heroicon-s-check-circle')
                                                ->requiresConfirmation()
                                                ->action(function ($state, $record) {
                                                    $state['status'] = StatusSubmissionEnum::REJECTED->value;
                                                    $record->update([
                                                        'notes' => $state['notes'],
                                                        'status' => $state['status'],
                                                    ]);
                                                    if ($record) {
                                                        Notification::make()
                                                            ->title('Saved successfully')
                                                            ->success()
                                                            ->send();
                                                    }
                                                    return redirect()->route('filament.admin.resources.submissions.index');
                                                }),
                                            Action::make('reject')
                                                ->label('Tolak')
                                                ->color('danger')
                                                ->icon('heroicon-s-x-circle')
                                                ->requiresConfirmation()
                                                ->action(function ($state, $record) {
                                                    $state['status'] = StatusSubmissionEnum::APPROVED->value;
                                                    $state['is_active'] = StatusSubmissionEnum::ACTIVE->value;
                                                    $record->update([
                                                        'notes' => $state['notes'],
                                                        'status' => $state['status'],
                                                        'is_active' => $state['is_active'],
                                                    ]);
                                                    if ($record) {
                                                        Notification::make()
                                                            ->title('Saved successfully')
                                                            ->success()
                                                            ->send();
                                                    }
                                                    return redirect()->route('filament.admin.resources.submissions.index');
                                                }),
                                        ])->alignCenter()
                                    ])
                                ])
                                ->columnSpan(2),
                        ])
                        ->columns(3)
                        ->operation('view')
                        // ->disabled()
                        ->model($this->getRecord())
                        ->statePath($this->getFormStatePath())
                        ->inlineLabel($this->hasInlineLabels()),
                ),
            ];
        }
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Section::make()
                            ->heading('Status Pengajuan')
                            ->schema([
                                Livewire::make(TimelineSubmission::class)
                            ]),
                        Section::make()
                            ->schema([
                                Section::make()
                                    ->extraAttributes(['class' => 'color-primary'])
                                    ->schema([
                                        ViewField::make('description')
                                            ->view('filament.forms.components.description-submission'),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Group::make(),
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
                                        Group::make()
                                    ])
                                    ->columns(3)
                            ])
                    ])
                    ->operation('view')
                    // ->disabled()
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath())
                    // ->columns($this->hasInlineLabels() ? 1 : 2)
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
