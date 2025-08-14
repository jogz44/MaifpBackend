<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // insert roles
        User::create([
            'first_name' => 'Deniel',
            'last_name' => 'Tomenio',
            'position' => 'Programmer',
            'status' => 'Active',
            'office' => 'CICTMO',
            'username' => 'Tomenio',
            'password' => 'admin',
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::create([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'position' => 'admin',
            'status' => 'Active',
            'office' => 'CICTMO',
            'username' => 'admin',
            'password' => 'admin',
            'role_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
