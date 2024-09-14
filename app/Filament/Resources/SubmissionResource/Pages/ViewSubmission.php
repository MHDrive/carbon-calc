<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Forms;
use App\Models\User;
use App\Enums\StatusSubmissionEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\Auth;
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

    protected function authorizeAccess(): void
    {
        $user = Auth::user(); // Dapatkan user yang sedang login

        if (!$user || $user->roles === UserRoleEnum::GUEST) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
    }


    protected function getForms(): array
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
                                Forms\Components\Section::make()
                                    ->schema(SubmissionResource::getMessageFormSchema()),
                            ])
                            ->columnSpan(1),
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Group::make()->schema(SubmissionResource::getDetailFormSchema()),
                                Forms\Components\Group::make([
                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('approve')
                                            ->label('Terima')
                                            ->color('success')
                                            ->icon('heroicon-s-check-circle')
                                            ->requiresConfirmation()
                                            ->action(function ($state, $record) {
                                                $state['status'] = StatusSubmissionEnum::APPROVED->value;
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
                                                        'title' => 'Selamat! Pengajuan Anda Diterima',
                                                        'body' => 'Pengajuan berkas Anda telah diterima oleh admin. Anda sudah bisa mengakses fitur kalkulator.'
                                                    ];
                                                    $this->notificationDatabase($record, $message);
                                                }
                                                return redirect()->route('filament.admin.resources.submissions.index');
                                            }),
                                        Forms\Components\Actions\Action::make('reject')
                                            ->label('Tolak')
                                            ->color('danger')
                                            ->icon('heroicon-s-x-circle')
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
                                                    $message = [
                                                        'title' => 'Maaf, Pengajuan Anda Ditolak',
                                                        'body' => 'Pengajuan berkas Anda ditolak oleh admin. Silakan klik lihat untuk melihat catatan penolakan dan ajukan ulang.'
                                                    ];
                                                    $this->notificationDatabase($record, $message);
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
        $sendTo = $record->with('user')->first();
        Notification::make()
            ->title($message['title'])
            ->body($message['body'])
            ->sendToDatabase($sendTo->user);

        event(new DatabaseNotificationsSent($sendTo->user));
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
