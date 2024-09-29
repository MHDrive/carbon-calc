<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages;

use Filament\Forms;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use App\Services\Classes\ApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Component;
use Filament\Resources\Pages\CreateRecord;
use Marvinosswald\FilamentInputSelectAffix\TextInputSelectAffix;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource;

class CreateKetterings2001Moist extends CreateRecord
{
    protected static string $resource = ClimateMoistResource::class;

    public $newData;

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Ketterings-2001';
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        Forms\Components\Section::make('Hidung Cadangan Karbon')
                            ->headerActions([
                                Forms\Components\Actions\Action::make('Riwayat')
                                    ->url(route('filament.admin.calculations.resources.climate-moists.history-ketterings-2001')),
                            ])
                            ->schema([
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\Group::make()
                                            ->schema([
                                                static::getResource()::getLocation(),
                                                $this->getRepeaterSampling(),
                                                $this->getRepeaterSensus(),
                                            ])->maxWidth('md') // mengatur ukuran form dan input
                                    ])->extraAttributes(['class' => 'flex justify-center']) // mengatur posisi form
                            ])
                    ])
                    ->operation('create')
                    ->model($this->getModel())
                    ->statePath($this->getFormStatePath()),
            ),
        ];
    }


    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        $data['meta'] = $data['meta_sampling'] ?? $data['meta_sensus'] ?? null;
        // Konversi surface_area berdasarkan unit yang dipilih
        if ($data['surface_area_unit'] == 'km²') {
            $forestArea = $data['surface_area'] * 1000000; // 1 km² = 1.000.000 m²
        } elseif ($data['surface_area_unit'] == 'ha') {
            $forestArea = $data['surface_area'] * 10000; // 1 ha = 10.000 m²
        } else {
            $forestArea = $data['surface_area']; // m²
        }

        $totalBiomassAllTonPerHa = 0; // Inisialisasi total biomassa keseluruhan
        $totalCarbonAllTonPerHa = 0;  // Inisialisasi total karbon keseluruhan
        $totalBiomassAllKgPerHa = 0; // Inisialisasi total biomassa keseluruhan
        $totalCarbonAllKgPerHa = 0;  // Inisialisasi total karbon keseluruhan
        $totalBiomassAll = 0; // Inisialisasi total biomassa keseluruhan
        $totalCarbonAll = 0;  // Inisialisasi total karbon keseluruhan

        foreach ($data['meta'] as &$meta) { // Gunakan & untuk mengubah nilai dalam array asli
            $totalBiomass = 0; // Biomassa per plot
            $totalCarbon = 0;  // Karbon per plot

            // Konversi plot_area berdasarkan unit yang dipilih di dalam meta
            if ($data['method'] == 'sampling') {
                if ($meta['plot_area_unit'] == 'km²') {
                    $plotArea = $meta['plot_area'] * 1000000; // 1 km² = 1.000.000 m²
                } elseif ($meta['plot_area_unit'] == 'ha') {
                    $plotArea = $meta['plot_area'] * 10000; // 1 ha = 10.000 m²
                } else {
                    $plotArea = $meta['plot_area']; // m²
                }
            }

            // Loop melalui input_calc untuk menjumlahkan biomass dan carbon per plot
            foreach ($meta['input_calc'] as &$input) {
                $input['biomass_perKg'] = round((0.11 * $input['wood_density'] * pow($input['diameter'], 2+0.62)), 2);
                $input['carbon_perKg'] = round(($input['biomass_perKg'] * 0.5), 2);

                $totalBiomass += $input['biomass_perKg']; // Tambahkan nilai biomass
                $totalCarbon += $input['carbon_perKg'];   // Tambahkan nilai karbon
            }

            if ($data['method'] === 'sampling') {
                // Jika metode adalah 'sampling', hitung biomassa dan karbon per plot dalam Ton/Ha
                $meta['total_biomass_perplot_tonPerHa'] = round(($totalBiomass / 1000) * (10000 / $plotArea), 2);
                $meta['total_carbon_perplot_tonPerHa'] = round(($totalCarbon / 1000) * (10000 / $plotArea), 2);
                $meta['total_biomass_perplot_kgPerHa'] = round(($totalBiomass) * (10000 / $plotArea), 2);
                $meta['total_carbon_perplot_kgPerHa'] = round(($totalCarbon) * (10000 / $plotArea), 2);

                // Tambahkan ke total keseluruhan
                $totalBiomassAllTonPerHa += $meta['total_biomass_perplot_tonPerHa'];
                $totalCarbonAllTonPerHa += $meta['total_carbon_perplot_tonPerHa'];

                $totalBiomassAllKgPerHa += $meta['total_biomass_perplot_kgPerHa'];
                $totalCarbonAllKgPerHa += $meta['total_carbon_perplot_kgPerHa'];
            } elseif ($data['method'] === 'sensus') {
                // Jika metode adalah 'sensus', tidak ada hitungan per plot, hitung langsung ke total keseluruhan
                $totalBiomassAll += $totalBiomass;
                $totalCarbonAll += $totalCarbon;
            }
        }

        if ($data['method'] === 'sensus') {
            // Jika metode adalah 'sensus', kalikan total keseluruhan dengan 10000/forest_area
            $totalBiomassAllTonPerHa = round(($totalBiomassAll / 1000) * (10000 / $forestArea), 2);
            $totalCarbonAllTonPerHa = round(($totalCarbonAll / 1000) * (10000 / $forestArea), 2);
            $totalBiomassAllKgPerHa = round(($totalBiomassAll) * (10000 / $forestArea), 2);
            $totalCarbonAllKgPerHa = round(($totalCarbonAll) * (10000 / $forestArea), 2);
        }

        // Simpan total keseluruhan ke dalam array data
        $data['total_biomass_tonPerHa'] = $totalBiomassAllTonPerHa;
        $data['total_carbon_stocks_tonPerHa'] = $totalCarbonAllTonPerHa;
        $data['total_biomass_kgPerHa'] = $totalBiomassAllKgPerHa;
        $data['total_carbon_stocks_kgPerHa'] = $totalCarbonAllKgPerHa;
        $data['climate_type'] = ClimateTypeEnum::MOIST;
        $data['formula_type'] = FormulaTypeEnum::KETTERINGS2001;
        $data['user_id'] = Auth::user()->id;

        // Buat record baru
        $record = static::getModel()::create($data);

        // Menyimpan meta ke dalam record jika perlu (jika menggunakan fungsi setManyMeta atau serupa)
        // $record->setManyMeta($data['meta']);

        $this->newData = $record;

        return $record;
    }





    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view-result', ['record' => $this->newData->id]);
    }

    public function getRepeaterSensus(): Component
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
                $this->getSubRepeater(),
            ]);
    }
    public function getRepeaterSampling(): Component
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
                $this->getSubRepeater(),
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
                    ->options(fn(Get $get) => ApiService::getTaxaTree($get('tree_name'))),
                Forms\Components\TextInput::make('wood_density') // keliling
                    ->label('Berat Jenis')
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
                    ->numeric()
                    ->suffix('g/cm⁻³'),
                Forms\Components\TextInput::make('circumference') // keliling
                    ->label('Keliling')
                    ->numeric() // hanya untuk number
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('diameter', round($state / 3.14, 2));
                    })
                    ->suffix('cm'),
                Forms\Components\TextInput::make('diameter') // diameter
                    ->label('Diameter')
                    ->formatStateUsing(fn ($state) => $state = (float) $state)
                    ->numeric()
                    ->live(debounce: 500) // keliling
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('circumference', 3.14 * $state);
                    })
                    ->suffix('cm'),


            ]);
    }
}
