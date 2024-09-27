<?php

namespace App\Enums;

use App\Services\Enums\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum FormulaTypeEnum: string implements HasLabel
{
    use UsefulEnums;

    case BROWN1995 = 'BROWN, 1995';
    case BROWN1997 = 'BROWN, 1997';
    case KETTERINGS2001 = 'KETTERINGS, 2001';

    public function getLabel(): string
    {
        return match ($this) {
            self::BROWN1995 => 'BROWN, 1995',
            self::BROWN1997 => 'BROWN, 1997',
            self::KETTERINGS2001 => 'KETTERINGS, 2001',
        };
    }
}
