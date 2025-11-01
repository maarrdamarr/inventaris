<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administratorPermissions = Permission::all();

        $staffPermissions = $administratorPermissions->reject(function ($permission) {
            return in_array($permission->name, [
                'lihat pengguna', 'tambah pengguna', 'ubah pengguna', 'hapus pengguna',
                'lihat peran dan hak akses', 'tambah peran dan hak akses', 'ubah peran dan hak akses', 'hapus peran dan hak akses',
                'kelola peminjaman', 'kelola kerusakan',
            ]);
        });

        $csPermissions = Permission::whereIn('name', [
            'kelola kerusakan',
            'lihat barang',
        ])->get();

        $studentPermissions = Permission::whereIn('name', [
            'lapor kerusakan',
        ])->get();

        $adminRole = Role::where('name', 'Administrator')->first();
        $staffRole = Role::where('name', 'Staff TU (Tata Usaha)')->first();
        $csRole = Role::where('name', 'CS Sekolah')->first();
        $studentRole = Role::where('name', 'Siswa')->first();

        if ($adminRole) $adminRole->syncPermissions($administratorPermissions);
        if ($staffRole) $staffRole->syncPermissions($staffPermissions);
        if ($csRole) $csRole->syncPermissions($csPermissions);
        if ($studentRole) $studentRole->syncPermissions($studentPermissions);
    }
}
