<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Submission;
use App\Enums\UserRoleEnum;
use App\Mail\CreateSubmissionMail;
use Filament\Forms\Components\View;
use App\Livewire\TimelineSubmission;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
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

    protected function authorizeAccess(): void
    {
        $user = Auth::user(); // Dapatkan user yang sedang login

        if (!$user || $user->roles === UserRoleEnum::ADMIN) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
    }

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
        $admin = User::where('roles', UserRoleEnum::ADMIN)->first();
        $this->notificationDatabase($user, $admin);
        // $this->sendToMail($admin, $record);
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view-guest', ['record' => $this->newData->id]);
    }

    public function notificationDatabase($user, $admin)
    {
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

    public function sendToMail($admin, $submission)
    {
        $details = [
            'subject' => 'Your Submission is Received',
            'body' => 'Thank you for your submission. We will review it and get back to you shortly.'
        ];

        // Kirim email
        $response = Mail::to($admin->email)
            ->send(new CreateSubmissionMail($details, $submission));
        dd($response);
    }
}
