<?php

namespace App\Enums;

use App\Services\Enums\UsefulEnums;
use Filament\Support\Contracts\HasLabel;

enum ClimateTypeEnum: string implements HasLabel
{
    use UsefulEnums;

    case DRY = 'dry';
    case WET = 'wet';
    case MOIST = 'moist';

    public function getLabel(): string
    {
        return match ($this) {
            self::DRY => 'Iklim Kering',
            self::WET => 'Iklim Basah',
            self::MOIST => 'Iklim Lembab',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::DRY => 'Zona iklim kering, dengan curah hujan kurang dari 1500 mm per tahun, umumnya memiliki vegetasi yang terbatas dan kering. Wilayah ini sering didominasi oleh padang rumput, semak belukar, atau hutan yang jarang. Produktivitas primer (pertumbuhan biomassa) di daerah ini cenderung rendah karena keterbatasan air. Akibatnya, akumulasi dan penyimpanan karbon dalam biomassa, terutama di atas tanah, juga terbatas.',
            self::WET => 'Zona iklim basah dengan curah hujan yang sangat tinggi umumnya didominasi oleh hutan hujan tropis yang lebat dan produktif. Produktivitas primer (pertumbuhan biomassa) di daerah ini sangat tinggi berkat ketersediaan air yang melimpah. Akibatnya, akumulasi dan penyimpanan karbon dalam biomassa, terutama di atas tanah, juga cenderung sangat besar.',
            self::MOIST => 'Zona iklim lembab dengan curah hujan yang tinggi umumnya memiliki vegetasi yang lebat dan produktif. Wilayah ini sering didominasi oleh hutan hujan tropis atau hutan campuran yang subur. Produktivitas primer (pertumbuhan biomassa) di daerah ini cenderung tinggi karena ketersediaan air yang melimpah. Akibatnya, akumulasi dan penyimpanan karbon dalam biomassa, terutama di atas tanah, juga cukup besar.',
        };
    }
}
