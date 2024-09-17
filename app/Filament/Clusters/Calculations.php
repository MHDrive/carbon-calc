<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;
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
}
