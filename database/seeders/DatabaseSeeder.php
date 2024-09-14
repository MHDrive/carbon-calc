<?php

namespace Database\Seeders;

use App\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Permission;
use App\Enums\UserRoleEnum;
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
            'email' => 'guest@gmail.com',
            'roles' => UserRoleEnum::GUEST
        ]);

        // $guest->syncRoles(UserRoleEnum::GUEST);
    }
}
