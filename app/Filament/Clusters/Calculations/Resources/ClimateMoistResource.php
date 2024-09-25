<?php

namespace App\Filament\Clusters\Calculations\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ClimateMoist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Filament\Clusters\Calculations;
use Filament\Forms\Components\Component;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\RelationManagers;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\EditClimateMoist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\ListClimateMoists;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\CreateClimateMoist;

class ClimateMoistResource extends Resource
{
    protected static ?string $model = ClimateMoist::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Calculations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hidung Cadangan Karbon')
                    ->headerActions([
                        Forms\Components\Actions\Action::make('Riwayat')
                            ->url(route('filament.admin.calculations.resources.climate-dries.history')),
                    ])
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('select_provinces') // ambil id dan update nama untuk di set ke input provinsi
                                            ->label('Provinsi')
                                            ->options(static::getProvinces()) // mengambil data api provinsi
                                            ->searchable()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $provinceData = static::getProvinces()[$state] ?? null;
                                                if ($provinceData) {
                                                    $set('provinces', $provinceData); // simpan nama provinsi
                                                }
                                                $set('select_regencies', null); // Reset kabupaten saat provinsi diubah
                                                $set('select_districts', null); // Reset kecamatan saat provinsi diubah
                                                $set('select_villages', null);  // Reset desa saat provinsi diubah
                                            })
                                            ->required(false),
                                        Forms\Components\Hidden::make('provinces'), // input hidden provinsi
                                        Forms\Components\Select::make('select_regencies') // ambil id dan update nama untuk di set ke input kabupaten
                                            ->label('Kabupaten')
                                            ->options(fn(Get $get) => static::getRegencies($get('select_provinces'))) // mengambil data api kabupaten
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $regencyData = static::getRegencies($get('select_provinces'))[$state] ?? null;
                                                if ($regencyData) {
                                                    $set('regencies', $regencyData); // Simpan nama kabupaten
                                                }
                                                $set('select_districts', null); // Reset kecamatan saat kabupaten diubah
                                                $set('select_villages', null);  // Reset desa saat kabupaten diubah
                                            })
                                            ->required(false),
                                        Forms\Components\Hidden::make('regencies'), // input hidden kabupaten
                                        Forms\Components\Select::make('select_districts') // ambil id dan update nama untuk di set ke input kec
                                            ->label('Kecamatan')
                                            ->options(fn(Get $get) => static::getDistricts($get('select_regencies'))) // mengambil data api kec
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $districtData = static::getDistricts($get('select_regencies'))[$state] ?? null;
                                                if ($districtData) {
                                                    $set('districts', $districtData); // Simpan nama kecamatan
                                                }
                                                $set('select_villages', null); // reset data desa
                                            })
                                            ->required(false),
                                        Forms\Components\Hidden::make('districts'), // hidden input kec
                                        Forms\Components\Select::make('select_villages') // ambil id dan update nama lalu set ke input desa
                                            ->label('Desa')
                                            ->options(fn(Get $get) => static::getVillages($get('select_districts'))) // mengambil data api desa
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $villageData = static::getVillages($get('select_districts'))[$state] ?? null;
                                                if ($villageData) {
                                                    $set('villages', $villageData); // Simpan nama desa
                                                }
                                            })
                                            ->required(false),
                                        Forms\Components\Hidden::make('villages'), // hidden input desa
                                        Forms\Components\TextInput::make('forest_name') // nama hutan
                                            ->minLength(3)
                                            ->maxLength(100)
                                            ->label('Nama Hutan'),
                                        TextInputSelectAffix::make('surface_area') // luas area hutan
                                            ->numeric()
                                            ->select(
                                                fn() => Forms\Components\Select::make('surface_area_unit') // select unit
                                                    ->extraAttributes([
                                                        'class' => 'w-[72px]' // lebar select unit
                                                    ])
                                                    ->default('m²') // nilai default
                                                    ->options([
                                                        'm²' => 'm²',
                                                        'km²' => 'km²',
                                                        'ha' => 'ha'
                                                    ])
                                            ),
                                        Forms\Components\Radio::make('method') // pilihan metode
                                            ->label('Pilih Metode')
                                            ->options([
                                                'sampling' => 'Sampling',
                                                'sensus' => 'Sensus',
                                            ])
                                            ->live(debounce: 300),
                                        static::getRepeaterSampling(),
                                        static::getRepeaterSensus(),
                                        // Forms\Components\Hidden::make('total_biomass'),
                                        // Forms\Components\Hidden::make('total_carbon'),

                                    ])->columns(2)->maxWidth('md') // mengatur ukuran form dan input
                            ])->extraAttributes(['class' => 'flex justify-center']) // mengatur posisi form
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('climate_type'),
                Tables\Columns\TextColumn::make('province')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subdistrict')
                    ->searchable(),
                Tables\Columns\TextColumn::make('village')
                    ->searchable(),
                Tables\Columns\TextColumn::make('forest_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('surface_area')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_plot')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_biomass')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_carbon_stocks')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClimateMoists::route('/'),
            'create' => CreateClimateMoist::route('/create'),
            'edit' => EditClimateMoist::route('/{record}/edit'),
        ];
    }

    public static function getProvinces()
    {
        return Cache::remember('provinces', 60 * 60, function () { // menggunakan cache untuk mengurangi beban api pada sistem
            $queryParams = [ // query api
                'sortOrder' => 'asc',
                'limit' => 38,
                'sortBy' => 'code',
            ];

            $response = Http::get('https://idn-area.up.railway.app/provinces', $queryParams); // link api
            if ($response->successful()) { // pengecekan api
                return collect($response->json()['data']) // hasil api yang dikembalikan berupa array id => name
                    ->pluck('name', 'code');
            }
            return []; // kembalikan array kosong apabila kondisi if bernilai false
        });
    }

    //method api kabupaten
    public static function getRegencies($provinceCode = null)
    {
        $cacheKey = 'regencies_' . ($provinceCode ?: 'all'); // nama cache

        return Cache::remember($cacheKey, 60 * 60, function () use ($provinceCode) { // cache menguarangi beban api pada sistem
            // Menggunakan Http Client Laravel untuk mengambil data dari API
            $queryParams = [
                'sortOrder' => 'asc',
                'sortBy' => 'code',
            ];

            // Jika ada data id provinsi, tambahkan ke query
            if ($provinceCode) {
                $queryParams['provinceCode'] = $provinceCode;
            }

            // Lakukan request ke API
            $response = Http::get('https://idn-area.up.railway.app/regencies', $queryParams);

            // Cek apakah response berhasil
            if ($response->successful()) {
                return collect($response->json()['data'])
                    ->pluck('name', 'code');
            }

            return [];
        });
    }


    // Method Api Kec
    public static function getDistricts($regencyCode = null)
    {
        $cacheKey = 'districts_' . ($regencyCode ?: 'all');

        return Cache::remember($cacheKey, 60 * 60, function () use ($regencyCode) {
            // Menggunakan Http Client Laravel untuk mengambil data dari API
            $queryParams = [
                'sortOrder' => 'asc',
                'sortBy' => 'code',
            ];

            if ($regencyCode) {
                $queryParams['regencyCode'] = $regencyCode;
            }

            // Lakukan request ke API
            $response = Http::get('https://idn-area.up.railway.app/districts', $queryParams);

            // Cek apakah response berhasil
            if ($response->successful()) {
                return collect($response->json()['data'])
                    ->pluck('name', 'code');
            }

            return [];
        });
    }

    // Method Api Desa
    public static function getVillages($districtCode = null)
    {
        $cacheKey = 'villages_' . ($districtCode ?: 'all');

        return Cache::remember($cacheKey, 60 * 60, function () use ($districtCode) {
            // Menggunakan Http Client Laravel untuk mengambil data dari API
            $queryParams = [
                'sortOrder' => 'asc',
                'sortBy' => 'code',
            ];

            if ($districtCode) {
                $queryParams['districtCode'] = $districtCode;
            }

            // Lakukan request ke API
            $response = Http::get('https://idn-area.up.railway.app/villages', $queryParams);

            // Cek apakah response berhasil
            if ($response->successful()) {
                return collect($response->json()['data'])
                    ->pluck('name', 'code');
            }

            return [];
        });
    }


    // nama ilmiah pohon select
    public static function getTaxaTree($qName = null)
    {
        // Jika tidak ada qName, kembalikan array kosong
        if (!$qName) {
            return [];
        }

        // Kunci cache harus unik berdasarkan qName
        $cacheKey = 'taxa_tree_' . $qName;

        // Cache selama 10 menit (bisa diubah sesuai kebutuhan)
        return Cache::remember($cacheKey, 60 * 60, function () use ($qName) {
            // Menggunakan Http Client Laravel untuk mengambil data dari API
            $queryParams = [
                'order' => 'asc',
                'locale' => 'indonesia',
                'order_by' => 'observations_count',
                'q' => $qName,
            ];

            // Lakukan request ke API
            $response = Http::get('https://api.inaturalist.org/v1/taxa', $queryParams);

            // Cek apakah response berhasil
            if ($response->successful()) {
                $trees = $response->json('results');
                $options = [];

                // Rank yang relevan untuk nama pohon dan ilmiahnya
                $validRanks = [
                    'species',
                    'genus',
                    'family',
                    'order',
                    'class',
                    'phylum'
                ];

                // Kelompokkan hasil berdasarkan rank yang relevan
                foreach ($trees as $tree) {
                    if (isset($tree['name']) && isset($tree['rank'])) {
                        $rank = $tree['rank'];
                        $scientificName = $tree['name'];

                        // Hanya tampilkan rank yang valid
                        if (in_array($rank, $validRanks)) {
                            // Buat grup rank jika belum ada
                            if (!isset($options[$rank])) {
                                $options[$rank] = [];
                            }

                            // Tambahkan nama ilmiah ke dalam grup berdasarkan rank
                            $options[$rank][$scientificName] = $scientificName;
                        }
                    }
                }

                return $options;
            }

            // Jika gagal, kembalikan array kosong
            return [];
        });
    }

    public static function getRepeaterSensus(): Component
    {
        return
            Forms\Components\Repeater::make('meta') // meta json untuk menampung data hitung
            ->hiddenLabel()
            ->visible(fn($get) => $get('method') === 'sensus') // show / !show
            ->columnSpanFull()
            ->addable(false)
            ->collapsible(false)
            ->addActionLabel('Tambah Data')
            ->reorderableWithDragAndDrop(false) // menghilangkan fitur drag and drop
            ->schema([
                static::getSubRepeater(),
            ]);
    }
    public static function getRepeaterSampling(): Component
    {
        return
            Forms\Components\Repeater::make('meta') // meta json untuk menampung data hitung
            ->hiddenLabel()
            ->visible(fn($get) => $get('method') === 'sampling') // show / !show
            ->columnSpanFull()
            ->collapsible()
            ->addActionLabel('Tambah Data')
            ->reorderableWithDragAndDrop(false) // menghilangkan fitur drag and drop
            ->afterStateHydrated(function (Forms\Components\Repeater $component, $state, Set $set) { // manipulasi data meta
                // Pastikan state adalah array
                if (is_array($state)) {
                    $keys = array_keys($state);
                    foreach ($keys as $index => $key) {
                        $set("meta.{$key}.plot", $index + 1);
                    }
                }
            })
            ->afterStateUpdated(function ($state, Set $set) { // manupulasi data meta
                // Jalankan setiap kali ada perubahan pada state repeater
                if (is_array($state)) {
                    $keys = array_keys($state);
                    foreach ($keys as $index => $key) {
                        $set("meta.{$key}.plot", $index + 1);
                    }
                }
            })
            ->schema([
                Forms\Components\TextInput::make('plot')
                    ->disabled(fn($get) => $get('../../method') == 'sampling' ? false : true), // disabled bila bukan sampling
                // Forms\Components\TextInput::make('plot_area')->suffix('m²'),
                TextInputSelectAffix::make('plot_area') // keliling
                    ->label('Luas Plot')
                    ->numeric() // hanya untuk number
                    ->select(
                        fn() => Forms\Components\Select::make('plot_area_unit') // select unit
                            ->extraAttributes([
                                'class' => 'w-[72px]'
                            ])
                            ->default('m²') // nilai default
                            ->options([
                                'm²' => 'm²',
                                'km²' => 'km²',
                                'ha' => 'ha'
                            ])
                    ),
                static::getSubRepeater(),
            ]);
    }

    public static function getSubRepeater(): Component
    {
        return Forms\Components\Repeater::make('input_calc')
            ->addActionLabel('Tambah Pohon')
            ->hiddenLabel()
            ->columnSpanFull()
            ->collapsed() // fitur dropdown
            ->schema([
                Forms\Components\TextInput::make('tree_name') // nama pohon
                    ->label('Nama Pohon')
                    ->live(onBlur: true)
                    ->required(),

                Forms\Components\Select::make('scientific_name') // nama ilmiah pohon
                    ->label('Nama Ilmiah Pohon')
                    ->options(fn(Get $get) => static::getTaxaTree($get('tree_name'))),
                Forms\Components\TextInput::make('circumference') // keliling
                    ->label('Keliling')
                    ->numeric() // hanya untuk number
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $diameter = $set('diameter', round($state / 3.14, 2));
                        $biomass = $set('biomass_perKg', round(0.139 * pow($diameter, 2.32)));
                        $set('carbon_perKg', round($biomass * 0.5));
                    })
                    ->suffix('cm'),
                Forms\Components\TextInput::make('diameter') // diameter
                    ->label('Diameter')
                    ->numeric()
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('circumference', 3.14 * $state);
                        $biomass = $set('biomass_perKg', round(0.139 * pow($state, 2.32)));
                        $set('carbon_perKg', round($biomass * 0.5));
                    })
                    ->suffix('cm'),
                Forms\Components\Hidden::make('biomass_perKg'), // nama pohon
                Forms\Components\Hidden::make('carbon_perKg') // nama pohon


            ]);
    }
}
