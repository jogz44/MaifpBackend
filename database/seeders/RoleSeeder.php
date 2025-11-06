<?php

namespace Database\Seeders;

use App\Models\role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // insert roles
        role::create([
            'role_name' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        role::create([
            'role_name' => 'doctor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        role::create([
            'role_name' => 'laboratory',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        role::create([
            'role_name' => 'social',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        role::create([
            'role_name' => 'coder',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        role::create([
            'role_name' => 'billing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        role::create([
            'role_name' => 'assessor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
