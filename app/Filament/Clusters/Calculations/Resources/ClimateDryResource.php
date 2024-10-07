<?php

namespace App\Filament\Clusters\Calculations\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\ClimateDry;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Services\Classes\ApiService;
use Illuminate\Support\Facades\Http;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Cache;
use App\Filament\Clusters\Calculations;
use Filament\Forms\Components\Component;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\RelationManagers;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\EditClimateDry;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\ViewClimateDry;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\CreateClimateDry;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\InfoClimateDries;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\ListClimateDries;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\CreateBrown1997Dry;
use Ramsey\Uuid\Type\Decimal;

class ClimateDryResource extends Resource
{
    // model
    protected static ?string $model = ClimateDry::class;

    // icon
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack'; // icon navigation

    protected static ?string $pluralModelLabel = 'Iklim Kering'; // plural label

    // position navigation sub sidebar
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    // parent cluster
    protected static ?string $cluster = Calculations::class;

    // method form
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
                                            ->options(ApiService::getProvinces()) // mengambil data api provinsi
                                            ->searchable()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $provinceData = ApiService::getProvinces()[$state] ?? null;
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
                                            ->options(fn(Get $get) => ApiService::getRegencies($get('select_provinces'))) // mengambil data api kabupaten
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $regencyData = ApiService::getRegencies($get('select_provinces'))[$state] ?? null;
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
                                            ->options(fn(Get $get) => ApiService::getDistricts($get('select_regencies'))) // mengambil data api kec
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $districtData = ApiService::getDistricts($get('select_regencies'))[$state] ?? null;
                                                if ($districtData) {
                                                    $set('districts', $districtData); // Simpan nama kecamatan
                                                }
                                                $set('select_villages', null); // reset data desa
                                            })
                                            ->required(false),
                                        Forms\Components\Hidden::make('districts'), // hidden input kec
                                        Forms\Components\Select::make('select_villages') // ambil id dan update nama lalu set ke input desa
                                            ->label('Desa')
                                            ->options(fn(Get $get) => ApiService::getVillages($get('select_districts'))) // mengambil data api desa
                                            ->live(onBlur: true)
                                            ->searchable()
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $villageData = ApiService::getVillages($get('select_districts'))[$state] ?? null;
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
                                            ->label('Luas Area')
                                            ->numeric()
                                            ->formatStateUsing(fn ($state) => $state = (float) $state)
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


    // method table
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('formula_type')
                    ->label('Rumus')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('forest_name')
                    ->label('Nama Hutan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('surface_area')
                    ->label('Luas Area')
                    ->getStateUsing(fn($record) => $record->surface_area . ' ' . $record->surface_area_unit)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('method')
                    ->label('Metode Hitung')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d F Y')
                    ->sortable(),
                 
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn($record) => route('filament.admin.calculations.resources.climate-dries.view-result', ['record' => $record->id])),
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
            'index' => InfoClimateDries::route('/'), // tampilan awal
            'history' => ListClimateDries::route('/history'), // thalaman history
            'brown-1997' => CreateBrown1997Dry::route('/brown-1997/create'), // halaman create/hitung
            'view-result' => ViewClimateDry::route('/history/{record}'), // halaman hasil create/hitung
        ];
    }

    // Method Api Provinsi




    // nama ilmiah pohon input
    // public static function getTaxaTree($search = null)
    // {
    //     // Menggunakan Http Client Laravel untuk mengambil data dari API
    //     $queryParams = [
    //         'order' => 'asc',
    //         'order_by' => 'observations_count',
    //     ];
    //     if ($search) {
    //         $queryParams['q'] = $search;
    //     }
    //     // Lakukan request ke API
    //     $response = Http::get('https://api.inaturalist.org/v1/taxa', $queryParams);
    //     // Cek apakah response berhasil
    //     if ($response->successful()) {
    //         $trees = $response->json('results');
    //         $options = '';

    //         // Formatkan hasil untuk ditampilkan di frontend
    //         foreach ($trees as $tree) {
    //             if (isset($tree['name'])) {
    //                 $options = $tree['name'] . ' (' . $tree['matched_term'] . ')'; // Menyimpan nama umum pohon
    //             }
    //         }

    //         return $options;
    //     }

    //     return '';
    // }

    public static function getRepeaterSensus(): Component
    {
        return
            Forms\Components\Repeater::make('meta_sensus') // meta json untuk menampung data hitung
            ->hiddenLabel()
            ->dehydrated(fn($get) => $get('method') === 'sensus')
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
            Forms\Components\Repeater::make('meta_sampling') // meta json untuk menampung data hitung
            ->hiddenLabel()
            ->dehydrated(fn($get) => $get('method') === 'sampling')
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
                        $set("meta_sampling.{$key}.plot", $index + 1);
                    }
                }
            })
            ->afterStateUpdated(function ($state, Set $set) { // manupulasi data meta
                // Jalankan setiap kali ada perubahan pada state repeater
                if (is_array($state)) {
                    $keys = array_keys($state);
                    foreach ($keys as $index => $key) {
                        $set("meta_sampling.{$key}.plot", $index + 1);
                    }
                }
            })
            ->schema([
                Forms\Components\TextInput::make('plot'), // disabled bila bukan sampling
                // Forms\Components\TextInput::make('plot_area')->suffix('m²'),
                TextInputSelectAffix::make('plot_area') // keliling
                    ->label('Luas Plot')
                    ->numeric() // hanya untuk number
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
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
                    ->required(false),

                Forms\Components\Select::make('scientific_name') // nama ilmiah pohon
                    ->label('Nama Ilmiah Pohon')
                    ->options(fn(Get $get) => ApiService::getTaxaTree($get('tree_name'))),
                Forms\Components\TextInput::make('circumference') // keliling
                    ->label('Keliling')
                    ->numeric() // hanya untuk number
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('diameter', round(((float)$state / 3.14), 2));
                    })
                    ->suffix('cm'),
                Forms\Components\TextInput::make('diameter') // diameter
                    ->label('Diameter')
                    ->numeric()
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('circumference', round((3.14 * (float)$state),2));
                    })
                    ->suffix('cm'),


            ]);
    }
}
