<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Submission;
use App\Enums\UserRoleEnum;
use Filament\Forms\Components\View;
use App\Livewire\TimelineSubmission;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Livewire;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    public $newData;

    // protected static string $view = 'filament.resources.pages.submission-create';

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
                ->heading('Status Pengajuan')
                ->schema([
                    View::make('livewire.timeline-submission')
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
                            Group::make()->schema(SubmissionResource::getMediaFormSchema()),
                            Group::make()
                        ])
                        ->columns(3)
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = auth()->user()->id;
        $record = new ($this->getModel())($data);

        $record->save();

        $this->newData = $record;
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->newData->id]);
    }
}
