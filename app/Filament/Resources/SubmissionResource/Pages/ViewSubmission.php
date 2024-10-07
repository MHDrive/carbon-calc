<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Forms;
use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Mail\CreateSubmissionMail;
use App\Enums\StatusSubmissionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\SubmissionResource;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Hugomyb\FilamentMediaAction\Forms\Components\Actions\MediaAction;

class ViewSubmission extends ViewRecord
{
    protected static string $resource = SubmissionResource::class;

    protected static ?string $title = 'Detail Pengajuan';

    public function getBreadcrumb(): string
    {
        return 'Detail';
    }

    protected function authorizeAccess(): void
    {
        $user = Auth::user(); // Dapatkan user yang sedang login

        if (!$user) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
    }


    protected function getForms(): array
    {
        if (Auth::user()->isGuest()) {
           $form = $this->FormGuest();
        }
        if (Auth::user()->isAdmin()) {
            $form = $this->FormAdmin();
        }
        return $form;
    }

    public function FormGuest(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('file_submission')
                                            ->hiddenLabel()
                                            ->deletable(false)
                                            ->collection('file_submissions')
                                            ->hintAction(
                                                MediaAction::make('View')
                                                    ->button()
                                                    // ->iconButton()
                                                    ->icon('heroicon-o-eye')
                                                    ->media(fn() => $this->getRecord()->getFirstMedia('file_submissions')->getUrl())
                                            )
                                    ]),
                                
                            ])
                            ->columnSpan(1),
                            Forms\Components\Section::make()
                                    ->schema(SubmissionResource::getMessageFormSchema())
                                    ->disabled()
                                    ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->operation('view')
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath())
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }
    public function FormAdmin(): array
    {
        $record = $this->getRecord();
        
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('file_submission')
                                            ->hiddenLabel()
                                            ->deletable(false)
                                            ->collection('file_submissions')
                                            ->hintAction(
                                                MediaAction::make('View')
                                                    ->button()
                                                    // ->iconButton()
                                                    ->icon('heroicon-o-eye')
                                                    ->media(fn() => $record->getFirstMedia('file_submissions')->getUrl())
                                            )
                                    ]),
                                Forms\Components\Section::make()
                                    ->schema(SubmissionResource::getMessageFormSchema())
                                    ->disabled(fn() => $record->status === StatusSubmissionEnum::APPROVED || $record->status === StatusSubmissionEnum::REJECTED),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Group::make()->schema(SubmissionResource::getDetailFormSchema())->disabled(),
                                Forms\Components\Group::make([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('approve')
                                            ->label('Terima')
                                            ->color('success')
                                            ->icon('heroicon-s-check-circle')
                                            ->requiresConfirmation()
                                            ->action(function ($state) use ($record) {
                                                $state['status'] = StatusSubmissionEnum::APPROVED->value;
                                                $record->update([
                                                    'notes' => $state['notes'],
                                                    'status' => $state['status'],
                                                    'granted_at' => now(),
                                                    'expires_at' => now()->addMonths(6),
                                                ]);
                                                if ($record->user) {
                                                    $record->user->update([
                                                        'access_calc' => true,
                                                    ]);
                                                }
                                                if ($record) {
                                                    Notification::make()
                                                        ->title('Saved successfully')
                                                        ->success()
                                                        ->send();
                                                    $message = [
                                                        'title' => 'Selamat! Pengajuan Anda Diterima',
                                                        'body' => 'Pengajuan berkas Anda telah diterima oleh admin. Anda sudah bisa mengakses fitur kalkulator.',
                                                        'url' => '/admin/calculations/',
                                                    ];
                                                    $this->notificationDatabase($record, $message);
                                                    $this->sendToMail($record, $message);
                                                }
                                                return redirect()->route('filament.admin.resources.submissions.index');
                                            }),
                                        Forms\Components\Actions\Action::make('reject')
                                            ->label('Tolak')
                                            ->color('danger')
                                            ->icon('heroicon-s-x-circle')
                                            ->requiresConfirmation()
                                            ->action(function ($state) use ($record)  {
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
                                                    $message = [
                                                        'title' => 'Maaf, Pengajuan Anda Ditolak',
                                                        'body' => 'Pengajuan berkas Anda ditolak oleh admin. Silakan klik lihat untuk melihat catatan penolakan dan ajukan ulang.',
                                                        'url' => '/admin/submissions/' . $record->id,
                                                    ];
                                                    $this->notificationDatabase($record, $message);
                                                    $this->sendToMail($record, $message);
                                                }
                                                return redirect()->route('filament.admin.resources.submissions.index');
                                            }),
                                    ])->alignCenter()
                                        ->hidden(fn() => $this->getRecord()->status === StatusSubmissionEnum::APPROVED || $this->getRecord()->status === StatusSubmissionEnum::REJECTED)
                                ])
                            ])
                            ->columnSpan(2),
                    ])
                    ->columns(3)
                    ->operation('view')
                    ->model($this->getRecord())
                    ->statePath($this->getFormStatePath())
                    ->inlineLabel($this->hasInlineLabels()),
            ),
        ];
    }

    public function notificationDatabase($record, array $message)
    {
        $sendTo = $record->with('user')->first(); // ambil data user dari relasi submission
        Notification::make()
            ->title($message['title'])
            ->body($message['body'])
            ->actions([
                Action::make('view')
                    ->label('lihat')
                    ->url(function () use ($record) {
                        if($record->status === StatusSubmissionEnum::APPROVED){
                            return route('filament.admin.calculations.resources.climate-dries.index');
                        }else{
                            return route('filament.admin.resources.submissions.view', ['record' => $record->id]);
                        }
                    })
                    ->button()
                    ->markAsRead(),
            ])
            ->sendToDatabase($sendTo->user);

        event(new DatabaseNotificationsSent($sendTo->user));
    }

    public function sendToMail($submission, $messages)
    {
        $users = $submission->with('user')->first(); // ambil data user dari relasi submission
        $messages['subject'] = 'Hasil_Pengajuan_Anda';
        $messages['regards'] = 'Hello ' . $users->user->first_name . ' ' . $users->user->last_name;
        // Kirim email
        Mail::to($users->user->email)
            ->send(new CreateSubmissionMail($messages, $submission, true));
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
