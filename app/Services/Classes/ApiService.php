<?php

namespace App\Services\Classes;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ApiService
{
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
}
