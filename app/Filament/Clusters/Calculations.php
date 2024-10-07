<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Auth;
use Filament\Pages\SubNavigationPosition;

class Calculations extends Cluster
{

    protected static ?string $clusterBreadcrumb = 'Kalkulator'; // nama breadcrumb di dalam header

    protected static ?string $navigationLabel = 'Kalkulator'; // nama navigasi di sidebar
    public static function getNavigationBadge(): ?string // set badge
    {
        return '!';
    }
    public static function getNavigationBadgeColor(): ?string // color badge
    {
        return 'primary';
    }
    public static function getNavigationBadgeTooltip(): ?string // tooltip badge
    {
        return '*Silakan cek rata-rata curah hujan di wilayahmu terlebih dahulu untuk menentukan kategori iklim yang sesuai untuk daerah tersebut.';
    }

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2'; // icon navigation di sidebar

    public static function canAccessClusteredComponents(): bool // akses ke komponen
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
