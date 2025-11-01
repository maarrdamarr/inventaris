<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@mail.com'],
            ['name' => 'Administrator', 'password' => bcrypt('secret')]
        );

        User::updateOrCreate(
            ['email' => 'stafftu@mail.com'],
            ['name' => 'Staff TU (Tata Usaha)', 'password' => bcrypt('secret')]
        );
    }
}
