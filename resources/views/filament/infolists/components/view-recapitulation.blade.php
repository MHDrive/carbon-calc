@php
    // dd($row->meta);
@endphp
<div>
    <h1 class="text-2xl font-bold">Detail</h1>
    <!-- Bagian Detail di atas Tabel -->
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <!-- Setiap detail akan berada dalam struktur flex -->
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Iklim</span>
            <span>: {{ $row->climate_type }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Rumus</span>
            <span>: {{ $row->formula_type }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Provinsi</span>
            <span>: {{ $row->provinces }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Kabupaten</span>
            <span>: {{ $row->regencies }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Kecamatan</span>
            <span>: {{ $row->districts }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Desa</span>
            <span>: {{ $row->villages }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Nama Hutan</span>
            <span>: {{ $row->forest_name }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Luas Area</span>
            <span>: {{ $row->surface_area }} {{ $row->surface_area_unit }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Metode</span>
            <span>: {{ $row->method }}</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Total Biomass</span>
            <span>: {{ $row->total_biomass_tonPerHa }} (Ton/Ha)</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Total Carbon</span>
            <span>: {{ $row->total_carbon_stocks_tonPerHa }} (Ton/Ha)</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Total Biomass</span>
            <span>: {{ $row->total_biomass_kgPerHa }} (Kg/Ha)</span>
        </div>
        <div class="flex">
            <span class="font-semibold min-w-[150px]">Total Carbon</span>
            <span>: {{ $row->total_carbon_stocks_kgPerHa }} (Kg/Ha)</span>
        </div>
    </div>
    <hr>
    <div
        class="divide-y mt-5 divide-gray-200 overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
            <table class="w-full table-auto text-xs divide-y divide-gray-200 text-start dark:divide-white/5">
                <thead class="bg-primary-600/50 dark:bg-primary-900/50 whitespace-nowrap">
                    <tr class="divide-x divide-y divide-gray-400 dark:divide-white/30">
                        <th colspan="8" style="font-weight: bold; text-align:center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Meta
                        </th>
                    </tr>
                    <tr class="divide-x divide-y divide-gray-400 dark:divide-white/30">
                        <th rowspan="2" style="font-weight: bold; text-align: center;" valign="middle"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal border-t border-gray-400 dark:border-white/30">
                            Plot</th>
                        <th rowspan="2" style="font-weight: bold; text-align: center;" valign="middle"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Luas
                            Plot
                        </th>
                        <th colspan="6" style="font-weight: bold; text-align:center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Inputan
                        </th>
                    </tr>
                    <tr class="divide-x divide-y divide-gray-400 dark:divide-white/30">
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal border-l border-t border-gray-400 dark:border-white/30">
                            Nama Pohon
                        </th>
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Nama
                            Ilmiah
                            Pohon</th>
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Berat (g/cm<sup>-3</sup>)
                        </th>
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Keliling (cm)
                        </th>
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Diameter (cm)
                        </th>
                        <th style="text-align: center;"
                            class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 w-10 whitespace-normal">Tinggi (m)
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 whitespace-nowrap dark:divide-white/5">
                    {{-- @foreach ($record as $row) --}}
                    @php
                        $totalRowspan = 0;
                        foreach ($row->meta as $meta) {
                            $totalRowspan += count($meta['input_calc']);
                        }
                    @endphp

                    <!-- Row utama dengan rowspan -->
                    <tr
                        class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                        @php $firstMeta = $row->meta[0]; @endphp
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4"
                            rowspan="{{ count($firstMeta['input_calc']) }}" align="center" valign="middle">
                            {{ $firstMeta['plot'] ?? '-' }}</td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4"
                            rowspan="{{ count($firstMeta['input_calc']) }}" align="left" valign="middle">
                            {{ $firstMeta['plot_area'] ?? '-' }}</td>
                        <!-- Data input_calc pertama -->
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['tree_name'] ?? '-' }}</td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['scientific_name'] ?? '-' }}
                        </td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['wood_density'] ?? '-' }}
                        </td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['circumference'] ?? '-' }}
                        </td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['diameter'] ?? '-' }}</td>
                        <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left" valign="middle">
                            {{ $firstMeta['input_calc'][0]['tree_height'] ?? '-' }}
                        </td>
                    </tr>

                    <!-- Baris selanjutnya untuk input_calc -->
                    @for ($i = 1; $i < count($firstMeta['input_calc']); $i++)
                        <tr
                            class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4 border-l border-gray-200 dark:border-white/5"
                                align="left" valign="middle">
                                {{ $firstMeta['input_calc'][$i]['tree_name'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $firstMeta['input_calc'][$i]['scientific_name'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $firstMeta['input_calc'][$i]['wood_density'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $firstMeta['input_calc'][$i]['circumference'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $firstMeta['input_calc'][$i]['diameter'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $firstMeta['input_calc'][$i]['tree_height'] ?? '-' }}
                            </td>
                        </tr>
                    @endfor

                    <!-- Baris untuk meta selanjutnya -->
                    @for ($metaIndex = 1; $metaIndex < count($row->meta); $metaIndex++)
                        @php $meta = $row->meta[$metaIndex]; @endphp
                        <tr
                            class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4"
                                rowspan="{{ count($meta['input_calc']) }}" align="center" valign="middle">
                                {{ $meta['plot'] }}</td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4"
                                rowspan="{{ count($meta['input_calc']) }}" align="left" valign="middle">
                                {{ $meta['plot_area'] }}</td>

                            <!-- Data input_calc pertama dari meta -->
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['tree_name'] ?? '-' }}</td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['scientific_name'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['wood_density'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['circumference'] ?? '-' }}
                            </td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['diameter'] ?? '-' }}</td>
                            <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                valign="middle">
                                {{ $meta['input_calc'][0]['tree_height'] ?? '-' }}
                            </td>
                        </tr>

                        <!-- Baris selanjutnya untuk input_calc -->
                        @for ($i = 1; $i < count($meta['input_calc']); $i++)
                            <tr
                                class="divide-x divide-gray-200 dark:divide-white/5 [@media(hover:hover)]:transition [@media(hover:hover)]:duration-75">
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">{{ $meta['input_calc'][$i]['tree_name'] ?? '-' }}
                                </td>
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">
                                    {{ $meta['input_calc'][$i]['scientific_name'] ?? '-' }}
                                </td>
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">
                                    {{ $meta['input_calc'][$i]['wood_density'] ?? '-' }}
                                </td>
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">
                                    {{ $meta['input_calc'][$i]['circumference'] ?? '-' }}
                                </td>
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">{{ $meta['input_calc'][$i]['diameter'] ?? '-' }}
                                </td>
                                <td class="px-2 py-2 sm:first-of-type:ps-4 sm:last-of-type:pe-4" align="left"
                                    valign="middle">
                                    {{ $meta['input_calc'][$i]['tree_height'] ?? '-' }}
                                </td>
                            </tr>
                        @endfor
                    @endfor
                    {{-- @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>
