<?php

namespace App\Filament\Clusters\Calculations\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\ClimateDry;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Http;
use Filament\Support\Enums\Alignment;
use App\Filament\Clusters\Calculations;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\RelationManagers;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\EditClimateDry;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\CreateClimateDry;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\InfoClimateDries;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages\ListClimateDries;
use Filament\Forms\Set;

class ClimateDryResource extends Resource
{
    protected static ?string $model = ClimateDry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static ?string $cluster = Calculations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Hidung Cadangan Karbon')
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Select::make('provinces')
                                            ->label('Provinsi')
                                            // ->options(static::getProvinces())
                                            // ->searchable()
                                            // ->getSearchResultsUsing(fn(string $search) => static::getProvinces($search))
                                            // ->live(onBlur: true)
                                            ->required(false),
                                        Forms\Components\Select::make('regencies')
                                            ->label('Kabupaten')
                                            // ->options(fn($get) => static::getRegencies($get('provinces')))
                                            // ->live(onBlur: true)
                                            // ->searchable()
                                            // ->getSearchResultsUsing(fn(string $search, $get) => static::getRegencies($get('provinces'), $search))
                                            ->required(false),
                                        Forms\Components\Select::make('districts')
                                            ->label('Kecamatan')
                                            // ->options(fn($get) => static::getDistricts($get('regencies')))
                                            // ->live(onBlur: true)
                                            // ->searchable()
                                            // ->getSearchResultsUsing(fn(string $search, $get) => static::getDistricts($get('regencies'), $search))
                                            ->required(false),
                                        Forms\Components\Select::make('villages')
                                            ->label('Desa')
                                            // ->options(fn($get) => static::getVillages($get('districts')))
                                            // ->live(onBlur: true)
                                            // ->searchable()
                                            // ->getSearchResultsUsing(fn(string $search, $get) => static::getVillages($get('districts'), $search))
                                            ->required(false),
                                        Forms\Components\TextInput::make('forest_name')
                                            ->label('Nama Hutan')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('surface_area')
                                            ->label('Luas Hutan')
                                            ->numeric()
                                            ->rules(['int']),
                                        Forms\Components\Radio::make('method')
                                            ->label('Pilih Metode')
                                            ->options([
                                                'sampling' => 'Sampling',
                                                'sensus' => 'Sensus',
                                            ])
                                            ->live(debounce: 300),
                                        Forms\Components\Repeater::make('meta')
                                            ->hiddenLabel()
                                            ->columnSpanFull()
                                            ->addActionLabel('Tambah Data')
                                            ->reorderableWithDragAndDrop(false)
                                            ->defaultItems(0)
                                            ->afterStateHydrated(function (Forms\Components\Repeater $component, $state, callable $set, callable $get) {
                                                // Pastikan state adalah array
                                                // dd($data);
                                                if (is_array($state)) {
                                                    // Dapatkan array key numerik untuk mengakses item
                                                    $keys = array_keys($state);
                                                    foreach ($keys as $index => $key) {
                                                        $set("meta.{$key}.plot", $index + 1);
                                                    }
                                                }
                                            })
                                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                // Jalankan setiap kali ada perubahan pada state repeater
                                                if (is_array($state)) {
                                                    $keys = array_keys($state);
                                                    foreach ($keys as $index => $key) {
                                                        $set("meta.{$key}.plot", $index + 1);
                                                    }
                                                }
                                            })
                                            ->collapsed()
                                            ->schema([
                                                Forms\Components\TextInput::make('plot')
                                                    ->disabled(fn($get) => $get('../../method') == 'sampling' ? false : true)
                                                    ->hidden(fn($get) => $get('../../method') == 'sampling' ? false : true),
                                                Forms\Components\Repeater::make('plot_input')
                                                    ->addActionLabel('Tambah Pohon')
                                                    ->hiddenLabel()
                                                    ->columnSpanFull()
                                                    ->collapsed()
                                                    ->schema([
                                                        Forms\Components\TextInput::make('tree_name')
                                                            ->label('Nama Pohon')
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(function ($state, $set) {
                                                                $data = static::getTaxaTree($state);
                                                                $set('scientific_name', $data);
                                                            })
                                                            ->required(),
                                                        Forms\Components\TextInput::make('scientific_name')
                                                            ->label('Nama Ilmiah Pohon')
                                                            ->disabled()
                                                            ->dehydrated(),
                                                        Forms\Components\TextInput::make('keliling'),
                                                        Forms\Components\TextInput::make('diameter'),

                                                    ]),
                                            ]),
                                    ])->columns(2)->maxWidth('md')
                            ])->extraAttributes(['class' => 'flex justify-center'])
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
            'index' => InfoClimateDries::route('/'),
            // 'index' => ListClimateDries::route('/'),
            'create' => CreateClimateDry::route('/create'),
            'edit' => EditClimateDry::route('/{record}/edit'),
        ];
    }

    // Method Api Provinsi
    public static function getProvinces($search = null)
    {
        // Menggunakan Http Client Laravel untuk mengambil data dari API
        // Tambahkan parameter pencarian jika ada
        $queryParams = [
            'sortOrder' => 'asc',
            'limit' => 7,   // Membatasi hanya 7 data yang ditampilkan awalnya
            'sortBy' => 'code',
        ];
        // Jika ada input pencarian, tambahkan ke query
        if ($search) {
            $queryParams['name'] = $search;
        }
        // Lakukan request ke API
        $response = Http::get('https://idn-area.up.railway.app/provinces', $queryParams);
        // Cek apakah response berhasil
        if ($response->successful()) {
            return collect($response->json()['data'])
                ->pluck('name', 'code');
        }
        return [];
    }

    //method api kabupaten
    public static function getRegencies($provinceCode = null, $search = null)
    {
        // Menggunakan Http Client Laravel untuk mengambil data dari API
        // Tambahkan parameter pencarian jika ada
        $queryParams = [
            'sortOrder' => 'asc',
            'limit' => 7,   // Membatasi hanya 7 data yang ditampilkan awalnya
            'sortBy' => 'code',
        ];
        // Jika ada input pencarian, tambahkan ke query
        if ($provinceCode) {
            $queryParams['provinceCode'] = $provinceCode;
        }
        if ($search) {
            $queryParams['name'] = $search;
        }
        // Lakukan request ke API
        $response = Http::get('https://idn-area.up.railway.app/regencies', $queryParams);
        // Cek apakah response berhasil
        if ($response->successful()) {
            return collect($response->json()['data'])
                ->pluck('name', 'code');
        }
        return [];
    }

    // Method Api Kec
    public static function getDistricts($regencyCode = null, $search = null)
    {
        // Menggunakan Http Client Laravel untuk mengambil data dari API
        // Tambahkan parameter pencarian jika ada
        $queryParams = [
            'sortOrder' => 'asc',
            'limit' => 7,   // Membatasi hanya 7 data yang ditampilkan awalnya
            'sortBy' => 'code',
        ];
        // Jika ada input pencarian, tambahkan ke query
        if ($regencyCode) {
            $queryParams['regencyCode'] = $regencyCode;
        }
        if ($search) {
            $queryParams['name'] = $search;
        }
        // Lakukan request ke API
        $response = Http::get('https://idn-area.up.railway.app/districts', $queryParams);
        // Cek apakah response berhasil
        if ($response->successful()) {
            return collect($response->json()['data'])
                ->pluck('name', 'code');
        }
        return [];
    }

    // Method Api Desa
    public static function getVillages($districtCode = null, $search = null)
    {
        // Menggunakan Http Client Laravel untuk mengambil data dari API
        // Tambahkan parameter pencarian jika ada
        $queryParams = [
            'sortOrder' => 'asc',
            'limit' => 7,   // Membatasi hanya 7 data yang ditampilkan awalnya
            'sortBy' => 'code',
        ];
        // Jika ada input pencarian, tambahkan ke query
        if ($districtCode) {
            $queryParams['districtCode'] = $districtCode;
        }
        if ($search) {
            $queryParams['name'] = $search;
        }
        // Lakukan request ke API
        $response = Http::get('https://idn-area.up.railway.app/villages', $queryParams);
        // Cek apakah response berhasil
        if ($response->successful()) {
            return collect($response->json()['data'])
                ->pluck('name', 'code');
        }
        return [];
    }

    // nama ilmiah pohon
    // public static function getTaxaTree($search = null)
    // {
    //     // Menggunakan Http Client Laravel untuk mengambil data dari API
    //     // Tambahkan parameter pencarian jika ada
    //     $queryParams = [
    //         'order' => 'asc',
    //         'order_by' => 'observations_count',
    //         'rank' => 'species',   // Membatasi hanya data khusus untuk species saja yang ditampilkan
    //     ];
    //     if ($search) {
    //         $queryParams['q'] = $search;
    //     }
    //     // Lakukan request ke API
    //     $response = Http::get('https://api.inaturalist.org/v1/taxa', $queryParams);
    //     // Cek apakah response berhasil
    //     if ($response->successful()) {
    //         $trees = $response->json('results');
    //         $options = [];

    //         // Formatkan hasil untuk ditampilkan di frontend
    //         foreach ($trees as $tree) {
    //             if (isset($tree['name'])) {
    //                 $options[] = $tree['name']; // Menyimpan nama umum pohon
    //             }
    //         }

    //         return $options;
    //     }

    //     return [];
    // }
    // nama ilmiah pohon
    public static function getTaxaTree($search = null)
    {
        // Menggunakan Http Client Laravel untuk mengambil data dari API
        // Tambahkan parameter pencarian jika ada
        $queryParams = [
            'order' => 'asc',
            'order_by' => 'observations_count',
            'rank' => 'species',   // Membatasi hanya data khusus untuk species saja yang ditampilkan
        ];
        if ($search) {
            $queryParams['q'] = $search;
        }
        // Lakukan request ke API
        $response = Http::get('https://api.inaturalist.org/v1/taxa', $queryParams);
        // Cek apakah response berhasil
        if ($response->successful()) {
            $trees = $response->json('results');
            $options = '';

            // Formatkan hasil untuk ditampilkan di frontend
            foreach ($trees as $tree) {
                if (isset($tree['name'])) {
                    $options = $tree['name']; // Menyimpan nama umum pohon
                }
            }

            return $options;
        }

        return [];
    }
}
