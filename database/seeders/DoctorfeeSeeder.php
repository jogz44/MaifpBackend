<?php

namespace Database\Seeders;

use App\Models\lib_doctor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorfeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // insert roles
        lib_doctor::create([
            'doctor_amount' => 150,
        ]);
    }
}
