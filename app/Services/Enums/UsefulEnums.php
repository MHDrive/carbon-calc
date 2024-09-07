<?php

namespace App\Services\Enums;

trait UsefulEnums
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function ($carry, $enum) {
            $carry[$enum->value] = $enum->getLabel();
            return $carry;
        }, []);
    }

    public static function random(): static
    {
        return self::from(self::values()[array_rand(self::values())]);
    }
}

