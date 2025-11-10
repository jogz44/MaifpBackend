<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Termwind\Components\Li;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

         $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            // PatientAllSeeder::class,
            LibLaboratorySeeder::class,
            DoctorfeeSeeder::class,
            LibRadiologySeeder::class,
            LibMammogramExaminationSeeder::class,
            LibLabExaminationSeeder::class,
            LibUltraSoundSeeder::class,

        ]);
    }
}
