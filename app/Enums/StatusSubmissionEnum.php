<?php

namespace App\Enums;

use App\Services\Enums\UsefulEnums;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusSubmissionEnum: string implements HasLabel, HasColor
{
    use UsefulEnums;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::APPROVED => 'Diterima',
            self::REJECTED => 'Ditolak',
        };
    }
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PENDING => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
