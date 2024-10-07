<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use Filament\Actions;
use App\Enums\ClimateTypeEnum;
use App\Enums\FormulaTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;

class CreateBrown1997Dry extends CreateRecord
{
    protected static string $resource = ClimateDryResource::class;

    public $newData;

    protected static ?string $title = 'Kalkulator Brown1997';

    public function getBreadcrumb(): string
    {
        return static::$breadcrumb ?? 'Brown-1997';
    }

    protected function handleRecordCreation(array $data): Model
    {
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
                $input['biomass_perKg'] = round((0.139 * pow($input['diameter'], 2.32)), 2);
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
        $data['climate_type'] = ClimateTypeEnum::DRY;
        $data['formula_type'] = FormulaTypeEnum::BROWN1997;
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
}
