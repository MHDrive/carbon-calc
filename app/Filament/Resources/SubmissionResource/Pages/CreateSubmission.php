<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Submission;
use App\Enums\UserRoleEnum;
use Filament\Actions\Action;
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
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;

class CreateSubmission extends CreateRecord
{
    protected static string $resource = SubmissionResource::class;

    protected static ?string $title = 'Pengajuan Berkas';

    public $newData;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function authorizeAccess(): void
    {
        $user = Auth::user(); // Dapatkan user yang sedang login
        $submission  = $user->submissions();
        $data = $submission->routeGuest()->first();
        if ($data) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
        if (!$user || $user->roles === UserRoleEnum::ADMIN) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
    }

    protected function handleRecordCreation(array $data): Model
    {
        $record = static::getModel()::create($data);
        $this->newData = $record;
        return $record;
    }

    protected function afterCreate(): void
    {
        $record = $this->newData->with('media')->first();
        $user = Auth::user();
        $admin = User::where('roles', UserRoleEnum::ADMIN)->first();
        $this->notificationDatabase($user, $admin);
        $this->sendToMail($user, $admin, $record);
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
                NotifAction::make('view')
                    ->label('lihat')
                    ->url(route('filament.admin.resources.submissions.view', ['record' => $this->newData->id]))
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($admin);

        event(new DatabaseNotificationsSent($admin));
    }

    public function sendToMail($user, $admin, $submission)
    {
        $messages = [
            'subject' => 'Pengajuan_Berkas_' . $user->first_name . '_' . $user->last_name,
            'body' => 'Kami telah menerima kiriman baru dari pengguna ' . $user->first_name . ' ' . $user->last_name . '.',
            'body2' => 'Jika Anda ingin melihat detailnya, klik tombol di bawah ini untuk masuk ke sistem:',
            'url' => '/admin/submissions/' . $submission->id,
            'title' => 'Pengajuan Baru',
        ];

        // Kirim email
        Mail::to($admin->email)
            ->send(new CreateSubmissionMail($messages, $submission));
    }

    protected function getCreateAnotherFormAction(): Action
    {
        return Action::make('createAnother')
            ->hidden(true);
    }
}
