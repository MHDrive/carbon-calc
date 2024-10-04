<?php

namespace App\Filament\Widgets;

use App\Enums\StatusSubmissionEnum;
use App\Models\User;
use App\Models\Submission;
use App\Models\Calculation;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 0;
    protected function getStats(): array
    {
        if (Auth::user()->isGuest()) {
            $climate_type = $this->filters['climate_type'] ?? null;
            $formula_type = $this->filters['formula_type'] ?? null;
            $method = $this->filters['method'] ?? null;

            $record = Calculation::query()->when($climate_type, function ($query) use ($climate_type) {
                return $query->where('climate_type', $climate_type);
            })
                ->when($formula_type, function ($query) use ($formula_type) {
                    return $query->where('formula_type', $formula_type);
                })
                ->when($method, function ($query) use ($method) {
                    return $query->where('method', $method);
                })->get();
            $carbon_ton = 0;
            $totalNamaPohonYangDihitung = 0;
            foreach ($record as $item) {
                $carbon_ton += $item->total_carbon_stocks_tonPerHa;
                foreach ($item->meta as $meta) {
                    foreach ($meta['input_calc'] as $input) {
                        if (!empty($input['tree_name'])) {
                            $totalNamaPohonYangDihitung++;
                        }
                    }
                }
            }

            return [
                Stat::make('Cadangan Karbon', $carbon_ton . 'Ton/Ha'),
                Stat::make('Pohon Yang Dihitung', $totalNamaPohonYangDihitung),
                Stat::make('Lokasi Yang Dihitung', $record->count())
            ];
        }
        if (Auth::user()->isAdmin()) {
            $submission = Submission::query();
            $user = User::query();

            $pengguna = $user->count();
            /**
             * menggunakan query selectraw atau sql mentah untuk mengoptimalkan query pada kasus ini 
             * yang mana kalau menggunakan orm akan membuat sistem menjadi lambat karna banyaknya query yang di jalankan
             */
            $submissionData = $submission 
                ->selectRaw('
        COUNT(*) as total,
        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as processed,
        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected
    ', [
                    StatusSubmissionEnum::PENDING,
                    StatusSubmissionEnum::APPROVED,
                    StatusSubmissionEnum::REJECTED,
                    StatusSubmissionEnum::APPROVED,
                    StatusSubmissionEnum::REJECTED
                ])
                ->first();

            // Ekstrak data dari query
            $pengajuan = $submissionData->total;
            $fileBelumDiproses = $submissionData->pending;
            $fileSudahDiproses = $submissionData->processed;
            $fileDiterima = $submissionData->approved;
            $fileDitolak = $submissionData->rejected;

            return [
                Stat::make('Pengguna', $pengguna),
                Stat::make('Pengajuan', $pengajuan),
                Stat::make('File Belum Diproses', $fileBelumDiproses),
                Stat::make('File Yang Sudah Diproses', $fileSudahDiproses),
                Stat::make('File Yang Diterima', $fileDiterima),
                Stat::make('File Yang Ditolak', $fileDitolak),
            ];
        }
    }
}
