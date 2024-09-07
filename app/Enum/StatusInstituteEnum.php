<?php

namespace App\Enum;

use App\Services\Enums\UsefulEnums;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusInstituteEnum: string implements HasColor, HasIcon, HasLabel
{
    use UsefulEnums;
    
    case PENDING = 'pending';
    case ACCREDITED = 'accredited';
    case NONACCREDITED = 'non-accredited';

    public function getLabel(): string
    {
            return match ($this) {
                self::PENDING => 'Pending',
                self::ACCREDITED => 'Terakreditasi',
                self::NONACCREDITED => 'Belum Terakreditasi',
            };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'default',
            self::ACCREDITED => 'success',
            self::NONACCREDITED => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PENDING => 'heroicon-m-arrow-path',
            self::ACCREDITED => 'heroicon-m-check-badge',
            self::NONACCREDITED => 'heroicon-m-x-circle',
        };
    }
}