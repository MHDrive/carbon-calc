<?php

namespace App\Filament\Resources\SubmissionResource\Pages;

use Filament\Actions;
use App\Enums\UserRoleEnum;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SubmissionResource;

class ListSubmissions extends ListRecords
{
    protected static string $resource = SubmissionResource::class;

    protected function authorizeAccess(): void
    {
        $user = Auth::user(); // Dapatkan user yang sedang login
        
        if (!$user || $user->roles === UserRoleEnum::GUEST) {
            abort(404); // Tampilkan 404 jika bukan admin
        }
    }
    protected function getHeaderActions(): array
    {
        if(Auth::user()->roles === UserRoleEnum::ADMIN){
            return [];
        }
        return [
            Actions\CreateAction::make()
        ];
    }
}
