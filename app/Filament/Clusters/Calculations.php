<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\SubNavigationPosition;

class Calculations extends Cluster
{
    public static function getNavigationBadge(): ?string
    {
        return '!';
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return '*Silakan cek rata-rata curah hujan di wilayahmu terlebih dahulu untuk menentukan kategori iklim yang sesuai untuk daerah tersebut.';
    }

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function canAccessClusteredComponents(): bool
    {
        $user = Auth::user();
        foreach (static::getClusteredComponents() as $component) {
            if ($component::canAccess() && $user->isGuest()) {
                if($user->access_calc){
                    return true;
                }
            }
        }

        return false;
    }
}
