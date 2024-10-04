@php
    // dd($data);
    $isSensus = $data['method'] === 'sensus';
    // konversi nilai luas hutan menjadi ha {
    if ($data['surface_area_unit'] === 'm²') {
        // apabila satuannya adalah mpersegi maka konversi menjadi ha
        $surface_area = $data['surface_area'] / 10000;
    }
    if ($data['surface_area_unit'] === 'km²') {
        // apabila satuannya kmpersegi maka konversi menjadi ha
        $surface_area = $data['surface_area'] / 100;
    }
    if ($data['surface_area_unit'] === 'ha') {
        // apabila satuannya ha maka set nilai kedalam variable surface_area
        $surface_area = $data['surface_area'];
    }
    // }

    // konversi nilai biomass dan carbon menjadi ton/ha {
    // if ($data['method'] === 'sampling') {
    //     // kondisi berdasarkan metode
    //     $biomass = ($data['total_biomass'] / 1000) * (10000 / $data['plot_area']);
    //     $biomass = ($data['total_carbon_stocks'] / 1000) * (10000 / $data['plot_area']);
    // } else {
    //     $biomass = round(($data['total_biomass'] / 1000) * (10000 / $data['surface_area']), 2);
    //     $carbon = round(($data['total_carbon_stocks'] / 1000) * (10000 / $data['surface_area']), 2);
    // }
    // }
@endphp
<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>

        <h1 class="text-2xl mb-4"><strong>Total Keseluruhan (Grand Total)</strong></h1>
        <div class="text-sm">
            <div class="flex items-center space-x-2 pl-5">
                <span class="w-32 text-gray-600/50 dark:text-gray-400">Hutan</span>
                <span>:</span>
                <span>{{ $data['forest_name'] }}</span>
            </div>
            <div class="pl-5 flex items-center space-x-2">
                <span class="w-32 text-gray-600/50 dark:text-gray-400">Luas Hutan</span>
                <span>:</span>
                <span>{{ $surface_area . ' ha' }}</span>
            </div>
            <div class="pl-5 flex items-center space-x-2">
                <span class="w-32 text-gray-600/50 dark:text-gray-400">Lokasi Hutan</span>
                <span>:</span>
                <span>
                    {{ ucwords(strtolower($data['villages'])) . // mengubah niai string agar khusus kalimat awal menjadi kapital
                        ', Kec. ' .
                        ucwords(strtolower($data['districts'])) .
                        ', Kab. ' .
                        ucwords(strtolower($data['regencies'])) .
                        ', Prov. ' .
                        ucwords(strtolower($data['provinces'])) }}
                </span>
            </div>
            <div class="pl-5 flex items-center space-x-2">
                <span class="w-32 text-gray-600/50 dark:text-gray-400">Tanggal</span>
                <span>:</span>
                <span>{{ \Carbon\Carbon::parse($data['created_at'])->translatedFormat('l, d M Y') }}</span>
            </div>
        </div>
        <!-- Menampilkan hasil total dalam div yang diinginkan -->
        <div class="flex flex-col pt-4">
            <span class="text-lg font-bold">Total Biomassa</span>
            <p class="text-4xl inline-flex"><span
                    class="text-primary-600">{{ number_format($data['total_biomass_tonPerHa'], 2) }}</span><strong>
                        &nbsp;Ton/Ha</strong></p>
            <span class="text-lg font-bold pt-4">Total Cadangan Karbon</span>
            <p class="text-4xl inline-flex"><span
                    class="text-primary-600">{{ number_format($data['total_carbon_stocks_tonPerHa'], 2) }}</span><strong>
                        &nbsp;Ton/Ha</strong>
            </p>
        </div>
        <hr class="border-2 my-4">
        <div x-data="{ open: '' }">
            <template x-if="open">
                <x-filament::button icon="heroicon-m-chevron-up" icon-position="after" x-on:click="open = !open">
                    Lihat Detail
                </x-filament::button>
            </template>
            <template x-if="!open">
                <x-filament::button icon="heroicon-m-chevron-down" icon-position="after" x-on:click="open = !open">
                    Lihat Detail
                </x-filament::button>
            </template>

            <div x-show="open" x-on:click.outside="open = false">
                <div
                    class="divide-y mt-5 divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
                    <div
                        class="relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                        <table
                            class="w-full table-auto text-xs divide-y divide-gray-200 text-start dark:divide-white/5">
                            <thead class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                <tr class="bg-primary-600/50 dark:bg-primary-900/50">
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">
                                        No</th>
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-12 whitespace-normal">
                                        Plot</th>
                                    <th class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 whitespace-normal">
                                        Nama Pohon</th>
                                    <th class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 whitespace-normal">
                                        Nama Ilmiah</th>
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-24 whitespace-normal">
                                        Jumlah Biomassa (Kg)</th>
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-24 whitespace-normal">
                                        Jumlah Cadangan Karbon (Kg)</th>
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-24 whitespace-normal">
                                        Jumlah Biomassa (Ton)</th>
                                    <th
                                        class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-24 whitespace-normal">
                                        Jumlah Cadangan Karbon (Ton)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                                @php
                                    $currentPlot = null;
                                @endphp

                                @foreach ($data['meta'] as $item)
                                    @if (!$isSensus && $item['plot'] != $currentPlot)
                                        @php
                                            $currentPlot = $item['plot'];
                                        @endphp
                                        <tr
                                            class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                                            <td colspan="8" class="text-left px-2 py-2">
                                                <strong>Plot: {{ $item['plot'] }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    @foreach ($item['input_calc'] as $calc)
                                        <tr
                                            class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                                            <td
                                                class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 text-center">
                                                {{ $loop->iteration }}</td>
                                            <td
                                                class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 text-center">
                                                {{ $item['plot'] ?? '-' }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ $calc['tree_name'] }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ $calc['scientific_name'] }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ $calc['biomass_perKg'] }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ $calc['carbon_perKg'] }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ round($calc['biomass_perKg'] / 1000, 2) }}</td>
                                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4">
                                                {{ round($calc['carbon_perKg'] / 1000, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    {{-- @dd($item['plot']) --}}
                                    @if ($currentPlot !== null)
                                        <tr class="bg-gray-300 dark:bg-gray-600">
                                            <td colspan="4" class="text-left font-bold px-2 py-2">Total Plot
                                                {{ $currentPlot }}</td>
                                            <td class="px-2 py-2 font-bold">
                                                {{ $item['total_biomass_perplot_kgPerHa'] }} Kg/ha</td>
                                            <td class="px-2 py-2 font-bold">
                                                {{ $item['total_carbon_perplot_kgPerHa'] }} Kg/ha</td>
                                            <td class="px-2 py-2 font-bold">
                                                {{ $item['total_biomass_perplot_tonPerHa'] }} Ton/ha</td>
                                            <td class="px-2 py-2 font-bold">
                                                {{ $item['total_carbon_perplot_tonPerHa'] }} Ton/ha</td>
                                        </tr>
                                    @endif
                                @endforeach
                                {{-- @if (!$isSensus)
                                    <tr class="bg-gray-300 dark:bg-gray-600">
                                        <td colspan="4" class="text-left font-bold px-2 py-2">Total Plot
                                            {{ $currentPlot }}</td>
                                        <td class="px-2 py-2 font-bold">
                                            {{ $item['total_biomass_perplot_kgPerHa'] }} Kg/ha</td>
                                        <td class="px-2 py-2 font-bold">
                                            {{ $item['total_carbon_perplot_kgPerHa'] }} Kg/ha</td>
                                        <td class="px-2 py-2 font-bold">
                                            {{ $item['total_biomass_perplot_tonPerHa'] }} Ton/ha</td>
                                        <td class="px-2 py-2 font-bold">
                                            {{ $item['total_carbon_perplot_tonPerHa'] }} Ton/ha</td>
                                    </tr>
                                @endif --}}
                                <tr class="bg-gray-400 dark:bg-gray-800">
                                    <td colspan="4" class="text-left font-bold px-2 py-2">Total Keseluruhan</td>
                                    <td class="px-2 py-2 font-bold">{{ $data['total_biomass_kgPerHa'] }} Kg/ha</td>
                                    <td class="px-2 py-2 font-bold">{{ $data['total_carbon_stocks_kgPerHa'] }} Kg/ha
                                    </td>
                                    <td class="px-2 py-2 font-bold">{{ $data['total_biomass_tonPerHa'] }} Ton/ha</td>
                                    <td class="px-2 py-2 font-bold">{{ $data['total_carbon_stocks_tonPerHa'] }} Ton/ha
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3 flex flex-row w-52 items-center justify-start group relative">
                    <x-filament::icon-button icon="heroicon-o-question-mark-circle" color="default" />
                    <span class="ml-2 cursor-pointer">Penjelasan Persatuan</span>

                    <!-- Tooltip -->
                    <div class="absolute bottom-full mb-2 hidden group-hover:flex justify-center w-80">
                        <div class="bg-gray-700 text-white text-sm p-4 rounded-lg shadow-lg">
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li><strong>Jumlah Biomassa Per Pohon (kg)(ton):</strong> Jumlah biomassa yang
                                    dihasilkan oleh satu pohon, dinyatakan dalam kilogram atau ton.</li>
                                <li><strong>Jumlah Cadangan Karbon Per Pohon (kg)(ton):</strong> Jumlah karbon yang
                                    tersimpan dalam satu pohon, dinyatakan dalam kilogram atau ton.</li>
                                <li><strong>Total Biomassa Per Plot (kg/ha)(ton/ha):</strong> Jumlah total biomassa dari
                                    semua pohon dalam satu plot, dihitung per hektar (ha), dinyatakan dalam kilogram per
                                    hektar atau ton per hektar.</li>
                                <li><strong>Total Cadangan Karbon Per Plot (kg/ha)(ton/ha):</strong> Jumlah total karbon
                                    yang tersimpan dalam semua pohon dalam satu plot, dihitung per hektar (ha),
                                    dinyatakan dalam kilogram per hektar atau ton per hektar.</li>
                                <li><strong>Total Keseluruhan Biomassa (kg/ha)(ton/ha):</strong> Jumlah total
                                    keseluruhan biomassa, dihitung per hektar (ha), dinyatakan dalam kilogram per hektar
                                    atau ton per hektar.</li>
                                <li><strong>Total Keseluruhan Cadangan Karbon (kg/ha)(ton/ha):</strong> Jumlah total
                                    karbon, dihitung per hektar (ha), dinyatakan dalam kilogram per hektar atau ton per
                                    hektar.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- {{ $getState() }} --}}
    </div>
</x-dynamic-component>
