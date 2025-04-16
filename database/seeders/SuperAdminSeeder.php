<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        DB::table('login')->insert([
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'rights' => 'SA',
            'active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
