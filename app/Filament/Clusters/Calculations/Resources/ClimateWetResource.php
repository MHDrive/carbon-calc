<?php

namespace App\Filament\Clusters\Calculations\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\ClimateWet;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Services\Classes\ApiService;
use App\Filament\Clusters\Calculations;
use Filament\Forms\Components\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\RelationManagers;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\EditClimateWet;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\InfoClimateWet;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\ViewClimateWet;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\ListClimateWets;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\CreateClimateWet;
use App\Filament\Clusters\Calculations\Resources\ClimateWetResource\Pages\CreateBrown1997Wet;

class ClimateWetResource extends Resource
{
    protected static ?string $model = ClimateWet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Calculations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hidung Cadangan Karbon')
                    ->headerActions([
                        Forms\Components\Actions\Action::make('Riwayat')
                            ->url(route('filament.admin.calculations.resources.climate-wets.history')),
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
                                            ->formatStateUsing(fn($state) => $state = (float) $state)
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
                                        //meta data
                                        static::getRepeaterSampling(),
                                        static::getRepeaterSensus(),
                                    ])->columns(2)->maxWidth('md') // mengatur ukuran form dan input
                            ])->extraAttributes(['class' => 'flex justify-center']) // mengatur posisi form
                    ])
            ]);
    }

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
                Tables\Actions\ViewAction::make()->url(fn($record) => route('filament.admin.calculations.resources.climate-wets.view-result', ['record' => $record->id])),
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
            'index' => InfoClimateWet::route('/'),
            'history' => ListClimateWets::route('/history'),
            'brown-1997' => CreateBrown1997Wet::route('/brown-1997/create'),
            'view-result' => ViewClimateWet::route('/{record}'),
        ];
    }

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
                    ->formatStateUsing(fn($state) => $state = (float) $state)
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
                    ->formatStateUsing(fn($state) => $state = (float) $state)
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('diameter', round(((float)$state / 3.14), 2));
                    })
                    ->suffix('cm'),
                Forms\Components\TextInput::make('diameter') // diameter
                    ->label('Diameter')
                    ->numeric()
                    ->formatStateUsing(fn($state) => $state = (float) $state)
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('circumference', round((3.14 * (float)$state), 2));
                    })
                    ->suffix('cm'),
                Forms\Components\TextInput::make('tree_height') // keliling
                    ->label('Tinggi')
                    ->formatStateUsing(fn($state) => $state = (float) $state)
                    ->numeric()
                    ->suffix('m'),


            ]);
    }
}
