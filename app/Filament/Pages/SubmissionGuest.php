<?php

namespace App\Filament\Pages;

use Exception;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Submission;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Services\Classes\SpatieMediaLibraryFileUpload;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;

class SubmissionGuest extends Page implements HasForms, HasActions, HasInfolists
{
    use InteractsWithForms, InteractsWithActions, InteractsWithInfolists;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.submission-guest';

    public $data;

    public $record;

    public function mount(Submission $record): void
    {
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('file_submission')
                            ->collection('file_submissions'),
                    ])
            ])
            ->operation('create')
            ->statePath('data');
    }

    protected function getSaveFormAction(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                // ->url()
                ->submit('form')
        ];
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();
        if (!$user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }
        return $user;
    }

    public function save()
    {
        $data = $this->form->getState();
        $data['user_id'] = $this->getUser()->id;
        $this->record->create($data);
        Notification::make()
            ->success()
            ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
            ->send();
    }
}
