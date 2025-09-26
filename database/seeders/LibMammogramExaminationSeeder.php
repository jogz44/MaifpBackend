<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibMammogramExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lib_mammogram_examination')->insert([
            [
                'procedure' => 'Digital Mammogram (4 views)',
                'rate' => 2000.00,
                'service_fee' => 200.00,
                'total_amount' => 200.00
            ],
            [
                'procedure' => 'Magnification View',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'procedure' => 'Wire Localization',
                'rate' => 1000.00,
                'service_fee' => 10.00,
                'total_amount' => 10.00
            ],
            [
                'procedure' => 'Tomosynthesis (15 images)',
                'rate' => 4000.00,
                'service_fee' => 400.00,
                'total_amount' => 400.00
            ],
            [
                'procedure' => 'Stereotactic Biopsy',
                'rate' => 6000.00,
                'service_fee' => 600.00,
                'total_amount' => 600.00
            ],
        ]);
    }
}
