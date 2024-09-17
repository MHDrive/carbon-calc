<?php

namespace Database\Seeders;

use App\Enums\ClimateTypeEnum;
use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Permission;
use App\Enums\UserRoleEnum;
use App\Models\Calculation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // $permissions = [
        //     "Profil:update",
        //     "Pengguna:viewAny",
        //     "Pengguna:view",
        //     "Pengguna:create",
        //     "Pengguna:update",
        //     "Pengguna:delete",
        //     "Role:viewAny",
        //     "Role:view",
        //     "Role:create",
        //     "Role:update",
        //     "Role:delete",
        //     "Instansi/Institusi:viewAny",
        //     "Instansi/Institusi:view",
        //     "Instansi/Institusi:create",
        //     "Instansi/Institusi:update",
        //     "Instansi/Institusi:delete",
        //     "Verifikasi-pengajuan:viewAny",
        //     "Verifikasi-pengajuan:view",
        //     "Verifikasi-pengajuan:create",
        //     "Verifikasi-pengajuan:update",
        //     "Verifikasi-pengajuan:delete",
        //     // "Kalkulator:viewAny",
        //     // "Kalkulator:view",
        //     // "Kalkulator:create",
        //     // "Kalkulator:update",
        //     // "Kalkulator:delete",
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission]);
        // }

        // foreach (UserRoleEnum::cases() as $role) {
        //     Role::create(['name' => $role, 'guard_name' => 'web']);
        // }

        // $roles = [
        //     "admin" => [
        //         "Profil:update",
        //         "Pengguna:viewAny",
        //         "Pengguna:view",
        //         "Pengguna:create",
        //         "Pengguna:update",
        //         "Pengguna:delete",
        //         "Role:viewAny",
        //         "Role:view",
        //         "Role:create",
        //         "Role:update",
        //         "Role:delete",
        //         "Instansi/Institusi:viewAny",
        //         "Instansi/Institusi:view",
        //         "Instansi/Institusi:create",
        //         "Instansi/Institusi:update",
        //         "Instansi/Institusi:delete",
        //         "Verifikasi-pengajuan:viewAny",
        //         "Verifikasi-pengajuan:view",
        //         "Verifikasi-pengajuan:create",
        //         "Verifikasi-pengajuan:update",
        //         "Verifikasi-pengajuan:delete",
        //         // "Kalkulator:viewAny",
        //         // "Kalkulator:view",
        //         // "Kalkulator:create",
        //         // "Kalkulator:update",
        //         // "Kalkulator:delete",
        //     ],
        //     "guest" => [
        //         "Profil:viewAny",
        //         "Profil:view",
        //         "Profil:update",
        //         "Instansi/Institusi:create",
        //         "Verifikasi-pengajuan:create",
        //     ],
        // ];

        // foreach ($roles as $roleName => $permissions) {
        //     if (empty($permissions)) {
        //         continue;
        //     }

        //     $role = Role::query()
        //         ->where('name', $roleName)
        //         ->first();

        //     $role->syncPermissions([...$permissions, ...$role->permissions->pluck('name')->toArray()]);
        // }

        $admin = User::factory()->create([
            'email' => 'myakunreal360@gmail.com',
            'roles' => UserRoleEnum::ADMIN
        ]);

        // $admin->syncRoles(UserRoleEnum::ADMIN);


        $guest = User::factory()->create([
            'email' => 'hamdj360@gmail.com',
            'roles' => UserRoleEnum::GUEST
        ]);

        // $guest->syncRoles(UserRoleEnum::GUEST);
        $data = [
            [
                'name' => 'BROWN, 1997',
                'description' => 'Rumus allometrik umum yang digunakan untuk menghitung biomassa hutan pada iklim kering adalah rumus Brown (1997). Rumus ini hanya memerlukan diameter pohon untuk menghitung biomassa. Rentang diameter yang digunakan berkisar antara 5 hingga 40 cm.',
                'climate_type' => ClimateTypeEnum::DRY,
            ],
            [
                'name' => 'BROWN, 1997',
                'description' => 'Rumus allometrik umum yang digunakan untuk menghitung  biomassa hutan pada iklim basah adalah rumus Brown (1997). Rumus ini memerlukan diameter dan tinggi pohon untuk menghitung biomassa. Rentang diameter yang digunakan berkisar antara 4 hingga 112 cm.',
                'climate_type' => ClimateTypeEnum::WET,
            ],
            [
                'name' => 'BROWN, 1997',
                'description' => 'Rumus allometrik umum yang digunakan untuk menghitung biomassa hutan pada iklim lembab adalah rumus Brown (1997). Rumus ini hanya memerlukan diameter pohon untuk menghitung biomassa. Rentang diameter yang digunakan berkisar antara 5 hingga 148 cm.',
                'climate_type' => ClimateTypeEnum::MOIST,
            ],
            [
                'name' => 'BROWN, 1995',
                'description' => 'Rumus allometrik umum yang digunakan untuk menghitung biomassa hutan pada iklim lembab adalah rumus Brown (1995). Rumus ini membutuhkan data berat jenis pohon, diameter pohon, dan tinggi pohon untuk menentukan biomassa.',
                'climate_type' => ClimateTypeEnum::MOIST,
            ],
            [
                'name' => 'KETTERINGS, 2001',
                'description' => 'Rumus allometrik umum yang digunakan untuk menghitung biomassa hutan pada iklim lembab adalah rumus Ketterings (2001). Rumus ini memerlukan berat jenis pohon dan diameter pohon untuk menghitung biomassa.',
                'climate_type' => ClimateTypeEnum::MOIST,
            ],
        ];
        foreach ($data as $item)
        {
            Calculation::factory()->create($item);
        }
    }
}
