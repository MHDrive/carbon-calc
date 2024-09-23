<?php

namespace App\Filament\Clusters\Calculations\Resources\ClimateDryResource\Pages;

use App\Enums\ClimateTypeEnum;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Calculations\Resources\ClimateDryResource;

class CreateClimateDry extends CreateRecord
{
    protected static string $resource = ClimateDryResource::class;

    public $newData;

    protected function handleRecordCreation(array $data): Model
    {
        $totalBiomassAll = 0; // Inisialisasi total biomass untuk plot ini
        $totalCarbonAll = 0; // Inisialisasi total biomass untuk plot ini
        foreach ($data['meta'] as $meta) { // Gunakan & untuk mengubah nilai dalam array asli
            $totalBiomass = 0;
            $totalCarbon = 0;

            foreach ($meta['input_calc'] as $input) {
                $totalBiomass += $input['biomass']; // Tambahkan nilai biomass
                $totalCarbon += $input['carbon']; // Tambahkan nilai biomass
            }

            if($data['method'] === 'sampling'){
                // Simpan total biomass per plot ke dalam kolom total_biomass_perplot
                $meta['total_biomass_perplot'] = $totalBiomass;
                $meta['total_carbon_perplot'] = $totalCarbon;
            }
            $totalBiomassAll += $totalBiomass; // Tambahkan total biomass ke dalam totalBiomassAll
            $totalCarbonAll += $totalCarbon; // 
        }
        $data['total_biomass'] = $totalBiomassAll;
        $data['total_carbon_stocks'] = $totalCarbonAll;
        $data['climate_type'] = ClimateTypeEnum::DRY;
        $record = static::getModel()::create($data);
        $this->newData = $record;
        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view-result', ['record' => $this->newData->id]);
    }
}
