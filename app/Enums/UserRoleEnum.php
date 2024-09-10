<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRoleEnum: string implements HasLabel, HasColor
{
    case ADMIN = 'admin';
    case GUEST = 'guest';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::GUEST => 'Guest',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ADMIN => 'success',
            self::GUEST => 'info',
        };
    }
}
