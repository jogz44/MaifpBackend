<?php

namespace Database\Seeders;

use App\Models\lib_mammogram_examination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibMammogramExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $data =[
            [
                'procedure' => 'Digital Mammogram (4 views)',
                'rate' => 2000.00,
                'service_fee' => 200.00,
                'total_amount' => 2200.00
            ],
            [
                'procedure' => 'Magnification View',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 550.00
            ],
            [
                'procedure' => 'Wire Localization',
                'rate' => 1000.00,
                'service_fee' => 10.00,
                'total_amount' => 1010.00
            ],
            [
                'procedure' => 'Tomosynthesis (15 images)',
                'rate' => 4000.00,
                'service_fee' => 400.00,
                'total_amount' => 4400.00
            ],
            [
                'procedure' => 'Stereotactic Biopsy',
                'rate' => 6000.00,
                'service_fee' => 600.00,
                'total_amount' => 6600.00
            ],
        ];

        lib_mammogram_examination::truncate();

        // Process and insert the data

        foreach ($data as $item) {
            $record = [
                'procedure' => $item['procedure'],
                'rate'    => $item['rate'],
                'service_fee'      => $item['service_fee'],
            ];

            // Calculate total_amount: selling_price + service_fee
            $record['total_amount'] = $record['rate'] + $record['service_fee'];

            lib_mammogram_examination::create($record);
        }
    }
}
