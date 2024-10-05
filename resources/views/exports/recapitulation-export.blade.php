@php
    // foreach ($record as $row) {
    //     $rowspan = 0;
    //     dd($row->meta[0]['input_calc'][0]);
    //     foreach ($row->meta as $meta) {
    //         $rowspan = count($meta['input_calc']);
    //     }
    //     dd($rowspan);
    //     // dd(count($row->meta));
    //     count($row->meta) + count($row->meta['input_calc']);
    // }
@endphp

{{-- <table>
    <thead>
        <tr>
            <th bgcolor="#f2f2f2" style="font-weight: bold; text-align: center;">Name</th>
            <th bgcolor="#f2f2f2" style="font-weight: bold; text-align: center;" colspan="2">Email</th>
        </tr>
    </thead>
    <tbody>
            <tr>
                <td data-type="string" bgcolor="#e8f5e9" align="left" valign="middle" style="font-size: 12px;">jjj</td>
                <td data-type="string" bgcolor="#e3f2fd" align="left" valign="middle" style="font-size: 12px;">hhh</td>
                <td data-type="string" align="center" valign="middle" style="border: 1px solid #ccc;">Additional Info</td>
            </tr>
    </tbody>
</table> --}}


<table>
    <thead>
        @if ($columns['meta'])
            <tr>
                @if ($columns['no'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">No</th>
                @endif
                @if ($columns['id'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">ID</th>
                @endif

                @if ($columns['climate_type'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Iklim</th>
                @endif

                @if ($columns['formula_type'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Rumus</th>
                @endif

                @if ($columns['address'])
                    @if ($columns['condition_address'] === 'perCell')
                        <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Provinsi</th>
                        <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Kabupaten</th>
                        <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Kecamatan</th>
                        <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Desa</th>
                    @else
                        <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Alamat</th>
                    @endif
                @endif

                @if ($columns['forest_name'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Nama Hutan</th>
                @endif

                @if ($columns['surface_area'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Luas Area</th>
                @endif

                @if ($columns['method'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Metode</th>
                @endif

                <th colspan="8" style="font-weight: bold; text-align:center;">Meta</th>

                @if ($columns['total_biomass_tonPerHa'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Total Biomass
                        (Ton/Ha)</th>
                @endif

                @if ($columns['total_carbon_stocks_tonPerHa'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Total Cadangan
                        Carbon (Ton/Ha)</th>
                @endif
                @if ($columns['total_biomass_kgPerHa'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Total Biomass
                        (Kg/Ha)</th>
                @endif

                @if ($columns['total_carbon_stocks_kgPerHa'])
                    <th rowspan="3" style="font-weight: bold; text-align: center;" valign="middle">Total Cadangan
                        Carbon (Kg/Ha)</th>
                @endif
            </tr>
            <tr>
                <th rowspan="2" style="font-weight: bold; text-align: center;" valign="middle">Plot</th>
                <th rowspan="2" style="font-weight: bold; text-align: center;" valign="middle">Luas Plot</th>
                <th colspan="6" style="font-weight: bold; text-align:center;">Inputan</th>
            </tr>
            <tr>
                <th style="text-align: center;">Nama Pohon</th>
                <th style="text-align: center;">Nama Ilmiah Pohon</th>
                <th style="text-align: center;">Berat Jenis</th>
                <th style="text-align: center;">Keliling</th>
                <th style="text-align: center;">Diameter</th>
                <th style="text-align: center;">Tinggi</th>
            </tr>
        @else
            <tr>
                @if ($columns['no'])
                    <th>No</th>
                @endif
                @if ($columns['id'])
                    <th>ID</th>
                @endif

                @if ($columns['climate_type'])
                    <th>Iklim</th>
                @endif

                @if ($columns['formula_type'])
                    <th>Rumus</th>
                @endif

                @if ($columns['address'])
                    @if ($columns['condition_address'] === 'perCell')
                        <th>Provinsi</th>
                        <th>Kabupaten</th>
                        <th>Kecamatan</th>
                        <th>Desa</th>
                    @else
                        <th>Alamat</th>
                    @endif
                @endif

                @if ($columns['forest_name'])
                    <th>Nama Hutan</th>
                @endif

                @if ($columns['surface_area'])
                    <th>Luas Area</th>
                @endif

                @if ($columns['method'])
                    <th>Metode</th>
                @endif

                @if ($columns['total_biomass_tonPerHa'])
                    <th>Total Biomass (Ton/Ha)</th>
                @endif

                @if ($columns['total_carbon_stocks_tonPerHa'])
                    <th>Total Cadangan Carbon (Ton/Ha)</th>
                @endif
                @if ($columns['total_biomass_kgPerHa'])
                    <th>Total Biomass (Kg/Ha)</th>
                @endif

                @if ($columns['total_carbon_stocks_kgPerHa'])
                    <th>Total Cadangan Carbon (Kg/Ha)</th>
                @endif
            </tr>
        @endif
    </thead>
    <tbody>
        @foreach ($record as $row)
            @php
                $totalRowspan = 0;
                if ($columns['meta']) {
                    foreach ($row->meta as $meta) {
                        $totalRowspan += count($meta['input_calc']);
                    }
                } else {
                    $totalRowspan = 1;
                }
            @endphp

            <!-- Row utama dengan rowspan -->
            <tr>
                @if ($columns['no'])
                    <td rowspan="{{ $totalRowspan }}" align="center" valign="middle">{{ $loop->iteration }}</td>
                @endif
                @if ($columns['id'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->id }}</td>
                @endif
                @if ($columns['climate_type'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->climate_type }}</td>
                @endif
                @if ($columns['formula_type'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->formula_type }}</td>
                @endif
                @if ($columns['address'])
                    @if ($columns['condition_address'] === 'perCell')
                        <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->provinces }}</td>
                        <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->regencies }}</td>
                        <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->districts }}</td>
                        <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->villages }}</td>
                    @else
                        <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->provinces }},
                            {{ $row->regencies }}, {{ $row->districts }}, {{ $row->villages }}</td>
                    @endif
                @endif

                @if ($columns['forest_name'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->forest_name }}</td>
                @endif
                @if ($columns['surface_area'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->surface_area }}
                        {{ $row->surface_area_unit }}</td>
                @endif
                @if ($columns['method'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">{{ $row->method }}</td>
                @endif

                @if ($columns['meta'])
                    @php $firstMeta = $row->meta[0]; @endphp
                    <td rowspan="{{ count($firstMeta['input_calc']) }}" align="center" valign="middle">
                        {{ $firstMeta['plot'] ?? '-' }}</td>
                    <td rowspan="{{ count($firstMeta['input_calc']) }}" align="left" valign="middle">
                        {{ $firstMeta['plot_area'] ?? '-' }}</td>
                    <!-- Data input_calc pertama -->
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['tree_name'] ?? '-' }}</td>
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['scientific_name'] ?? '-' }}
                    </td>
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['wood_density'] ?? '-' }}</td>
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['circumference'] ?? '-' }}</td>
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['diameter'] ?? '-' }}</td>
                    <td align="left" valign="middle">{{ $firstMeta['input_calc'][0]['tree_height'] ?? '-' }}</td>
                @endif

                @if ($columns['total_biomass_tonPerHa'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">
                        {{ $row->total_biomass_tonPerHa }}
                    </td>
                @endif

                @if ($columns['total_carbon_stocks_tonPerHa'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">
                        {{ $row->total_carbon_stocks_tonPerHa }}</td>
                @endif
                @if ($columns['total_biomass_kgPerHa'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">
                        {{ $row->total_biomass_kgPerHa }}
                    </td>
                @endif

                @if ($columns['total_carbon_stocks_kgPerHa'])
                    <td rowspan="{{ $totalRowspan }}" align="left" valign="middle">
                        {{ $row->total_carbon_stocks_kgPerHa }}</td>
                @endif
            </tr>

            @if ($columns['meta'])
                <!-- Baris selanjutnya untuk input_calc -->
                @for ($i = 1; $i < count($firstMeta['input_calc']); $i++)
                    <tr>
                        <td align="left" valign="middle">{{ $firstMeta['input_calc'][$i]['tree_name'] ?? '-' }}
                        </td>
                        <td align="left" valign="middle">
                            {{ $firstMeta['input_calc'][$i]['scientific_name'] ?? '-' }}
                        </td>
                        <td align="left" valign="middle">{{ $firstMeta['input_calc'][$i]['wood_density'] ?? '-' }}
                        </td>
                        <td align="left" valign="middle">{{ $firstMeta['input_calc'][$i]['circumference'] ?? '-' }}
                        </td>
                        <td align="left" valign="middle">{{ $firstMeta['input_calc'][$i]['diameter'] ?? '-' }}</td>
                        <td align="left" valign="middle">{{ $firstMeta['input_calc'][$i]['tree_height'] ?? '-' }}
                        </td>
                    </tr>
                @endfor

                <!-- Baris untuk meta selanjutnya -->
                @for ($metaIndex = 1; $metaIndex < count($row->meta); $metaIndex++)
                    @php $meta = $row->meta[$metaIndex]; @endphp
                    <tr>
                        <td rowspan="{{ count($meta['input_calc']) }}" align="center" valign="middle">
                            {{ $meta['plot'] }}</td>
                        <td rowspan="{{ count($meta['input_calc']) }}" align="left" valign="middle">
                            {{ $meta['plot_area'] }}</td>

                        <!-- Data input_calc pertama dari meta -->
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['tree_name'] ?? '-' }}</td>
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['scientific_name'] ?? '-' }}
                        </td>
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['wood_density'] ?? '-' }}</td>
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['circumference'] ?? '-' }}</td>
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['diameter'] ?? '-' }}</td>
                        <td align="left" valign="middle">{{ $meta['input_calc'][0]['tree_height'] ?? '-' }}</td>
                    </tr>

                    <!-- Baris selanjutnya untuk input_calc -->
                    @for ($i = 1; $i < count($meta['input_calc']); $i++)
                        <tr>
                            <td align="left" valign="middle">{{ $meta['input_calc'][$i]['tree_name'] ?? '-' }}</td>
                            <td align="left" valign="middle">
                                {{ $meta['input_calc'][$i]['scientific_name'] ?? '-' }}
                            </td>
                            <td align="left" valign="middle">{{ $meta['input_calc'][$i]['wood_density'] ?? '-' }}
                            </td>
                            <td align="left" valign="middle">{{ $meta['input_calc'][$i]['circumference'] ?? '-' }}
                            </td>
                            <td align="left" valign="middle">{{ $meta['input_calc'][$i]['diameter'] ?? '-' }}</td>
                            <td align="left" valign="middle">{{ $meta['input_calc'][$i]['tree_height'] ?? '-' }}
                            </td>
                        </tr>
                    @endfor
                @endfor
            @endif
        @endforeach
    </tbody>
</table>
