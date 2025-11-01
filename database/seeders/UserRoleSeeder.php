<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@mail.com')->first();
        if ($admin) {
            $admin->syncRoles(['Administrator']);
        }

        $staff = User::where('email', 'stafftu@mail.com')->first();
        if ($staff) {
            $staff->syncRoles(['Staff TU (Tata Usaha)']);
        }
    }
}
