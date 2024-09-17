@php
    $dryRecord = $dryRecord ?? null;
    $dryRecordFirst = $dryRecord->first() ?? null;
    if ($dryRecordFirst->climate_type == App\Enums\ClimateTypeEnum::DRY) {
        $climateDescription = App\Enums\ClimateTypeEnum::DRY->getDescription();
    } elseif ($dryRecordFirst->climate_type == App\Enums\ClimateTypeEnum::WET) {
        $climateDescription = App\Enums\ClimateTypeEnum::WET->getDescription();
    } else {
        $climateDescription = App\Enums\ClimateTypeEnum::MOIST->getDescription();
    }

@endphp
<x-filament-panels::page @class([
    'fi-resource-list-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div class="flex flex-col gap-y-6">
        <x-filament-panels::resources.tabs />
        <x-filament::section>
            <x-slot name="heading">
                Kategori Iklim
            </x-slot>
            <p class="font-semibold text-sm">{{ $climateDescription }}</p>
        </x-filament::section>
        <x-filament::section class="section-change-password">
            <x-slot name="heading">
                    Kategori Rumus
            </x-slot>

            <div class="flex items-center justify-center">
                @foreach ($dryRecord as $item)
                    <div
                        class="w-28 h-28 bg-[#E6EAE5] flex flex-col justify-between shadow-sm rounded-lg ring-1 ring-gray-950/50 dark:bg-gray-900 dark:ring-white/10 p-2">
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-sm font-bold">{{ $item->name }}</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="flex justify-center font-bold">
                                <x-filament::icon-button icon="heroicon-o-question-mark-circle"
                                    tooltip="{{ $item->description }}" size="xl" color="black" />
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
