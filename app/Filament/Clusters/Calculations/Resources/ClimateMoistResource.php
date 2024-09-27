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
use App\Services\Classes\ApiService;
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
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\InfoClimateMoist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\ViewClimateMoist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\ListClimateMoists;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\CreateClimateMoist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\CreateBrown1995Moist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\CreateBrown1997Moist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\HistoryBrown1995Moists;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\HistoryBrown1997Moists;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\CreateKetterings2001Moist;
use App\Filament\Clusters\Calculations\Resources\ClimateMoistResource\Pages\HistoryKetterings2001Moists;

class ClimateMoistResource extends Resource
{
    protected static ?string $model = ClimateMoist::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    protected static ?string $cluster = Calculations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn($record) => route('filament.admin.calculations.resources.climate-moists.view-result', ['record' => $record->id])),
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
            'index' => InfoClimateMoist::route('/'),
            'history-brown-1997' => HistoryBrown1997Moists::route('/brown-1997/history'),
            'history-brown-1995' => HistoryBrown1995Moists::route('/brown-1995/history'),
            'history-ketterings-2001' => HistoryKetterings2001Moists::route('/ketterings-2001/history'),
            'brown-1995' => CreateBrown1995Moist::route('/brown-1995/create'),
            'brown-1997' => CreateBrown1997Moist::route('/brown-1997/create'),
            'ketterings-2001' => CreateKetterings2001Moist::route('/ketterings-2001/create'),
            'view-result' => ViewClimateMoist::route('/{record}/view-result'),
        ];
    }
    public static function getLocation(): Component
    {
        return Forms\Components\Group::make()->schema([
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
        ])->columns(2);
    }
}
