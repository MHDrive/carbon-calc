<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Submission;
use App\Enums\UserRoleEnum;
use Filament\Forms\Components\View;
use App\Livewire\TimelineSubmission;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Livewire;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Events\DatabaseNotificationsSent;
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
                            Group::make([
                                Hidden::make('user_id')
                                    ->default(Auth::id())
                            ]),
                            SpatieMediaLibraryFileUpload::make('file_submission')
                                ->hiddenLabel()
                                ->collection('file_submissions'),
                            Group::make()
                        ])
                        ->columns(3)
                ]),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = Submission::create($data);
        $this->newData = $record;
        $user = Auth::user();
        $this->notificationDatabase($user);
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view-guest', ['record' => $this->newData->id]);
    }

    public function notificationDatabase($user)
    {
        $admin = User::where('roles', UserRoleEnum::ADMIN)->first();
        Notification::make()
            ->title('Pengajuan Berkas')
            ->body('Pengguna ' . $user->first_name . ' ' . $user->last_name . ' telah mengajukan berkas baru. Silakan klik lihat untuk melihat detail dan melakukan verifikasi.')
            ->actions([
                Action::make('view')
                    ->label('lihat')
                    ->url(route('filament.admin.resources.submissions.view', ['record' => $this->newData->id]))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admin);

        event(new DatabaseNotificationsSent($admin));
    }
}
