<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'superadmin@onexternal.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin', // tambahkan username
                'password' => Hash::make("3`[W)j!gJ;/'86<S"),
                'role' => 'superadmin',
            ]
        );
    }
}
