<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = ['Administrator','Staff TU (Tata Usaha)','CS Sekolah','Siswa'];
        foreach ($names as $name) {
            Role::findOrCreate($name, 'web');
        }
    }
}
