@php
    $climateDescription = App\Enums\ClimateTypeEnum::WET->getDescription();
@endphp
<x-filament-panels::page @class([
    'fi-resource-list-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div class="flex flex-col gap-y-6">
        <x-filament-panels::resources.tabs />
        <x-filament::section class="section-change-color">
            <x-slot name="heading">
                Kategori Iklim
            </x-slot>
            <div class="flex flex-row items-center justify-between">
                <div class="flex-grow w-full whitespace-nowrap mr-5">
                    <span class="text-xs font-semibold text-gray-600/50">
                        ZONA IKLIM
                    </span> <br>
                    <strong class="text-sm">
                        > 4000 </strong> <br>
                            <p class="text-xs text-primary-600 font-bold mt-2">Iklim Basah</p> <br>
                            <img src="{{ asset('images/icon/wet.png') }}" alt="">
                </div>
                <div>
                    <p class="font-semibold text-sm">{{ $climateDescription }}</p>
                </div>
            </div>

        </x-filament::section>
        <x-filament::section class="section-change-color">
            <x-slot name="heading">
                Kategori Rumus
            </x-slot>

            <div class="flex items-center justify-around">
                {{-- @foreach ($dryRecord as $item) --}}
                <a href="{{ route('filament.admin.calculations.resources.climate-wets.brown-1997') }}">
                    <div
                        class="w-28 h-28 bg-[#E6EAE5] flex flex-col justify-between shadow-sm rounded-lg ring-1 ring-gray-950/50 dark:bg-gray-900 dark:ring-white/10 p-2">
                        <div class="flex-1 flex items-center justify-center">
                            <span class="text-sm font-bold">BROWN, 1997</span>
                        </div>
                        <div class="flex items-center justify-center">
                            <span class="flex justify-center font-bold">
                                <x-filament::icon-button icon="heroicon-o-question-mark-circle"
                                    tooltip="Rumus allometrik umum yang digunakan untuk menghitung  biomassa hutan pada iklim basah adalah rumus Brown (1997). Rumus ini memerlukan diameter dan tinggi pohon untuk menghitung biomassa. Rentang diameter yang digunakan berkisar antara 4 hingga 112 cm."
                                    size="xl" color="black" />
                            </span>
                        </div>
                    </div>
                </a>
                {{-- @endforeach --}}
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
