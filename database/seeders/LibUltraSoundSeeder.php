<?php

namespace Database\Seeders;

use App\Models\lab_ultrasound_details;
use App\Models\lib_ultra_sound;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibUltraSoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the base data from the image (Rate and ESPF/Service Fee)
        $data = [
            ['body_parts' => 'Whole Abdomen', 'rate' => 900.00, 'service_fee' => 90.00],
            ['body_parts' => 'Whole Abdomen/Prostate', 'rate' => 980.00, 'service_fee' => 98.00],
            ['body_parts' => 'Whole Abdomen/Pelvis', 'rate' => 1000.00, 'service_fee' => 100.00],
            ['body_parts' => 'Upper Abdomen', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Lower Abdomen', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'HBT/Pancreas/Spleen', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'HBT/Pancreas', 'rate' => 550.00, 'service_fee' => 55.00],
            ['body_parts' => 'HBT', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Liver', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Spleen', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Pancreas', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'KUB', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Prostate', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'KUB/Prostate', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Pelvis', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Scrotum', 'rate' => 1200.00, 'service_fee' => 120.00],
            ['body_parts' => 'Inguinal-Scrotal', 'rate' => 2000.00, 'service_fee' => 200.00],
            ['body_parts' => 'Both Inguinal Area', 'rate' => 1200.00, 'service_fee' => 120.00],
            ['body_parts' => 'Inguinal Area (Right or Left)', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Both Hemothorax', 'rate' => 1000.00, 'service_fee' => 100.00],
            ['body_parts' => 'Hemothorax (Right or Left)', 'rate' => 500.00, 'service_fee' => 50.00],
            ['body_parts' => 'Cranial', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Thyroid', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Breast', 'rate' => 1200.00, 'service_fee' => 120.00],
            ['body_parts' => 'Para-Aortic Nodes', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Soft Tissue / Mass', 'rate' => 650.00, 'service_fee' => 65.00],
            ['body_parts' => 'Doppler, Upper Ext, Per Limb', 'rate' => 1800.00, 'service_fee' => 180.00],
            ['body_parts' => 'Carotid Doppler', 'rate' => 3500.00, 'service_fee' => 350.00],
            ['body_parts' => 'Renal Graft', 'rate' => 2000.00, 'service_fee' => 200.00],
            ['body_parts' => 'Renal Doppler', 'rate' => 2500.00, 'service_fee' => 250.00],
        ];

        // Clear existing data to prevent duplicates on reruns
       lab_ultrasound_details::truncate();

        // Process and insert the data
        foreach ($data as $item) {
            // Calculate total_amount as requested: rate + service_fee
            $item['total_amount'] = $item['rate'] + $item['service_fee'];

            lib_ultra_sound::create($item);
        }
    }
}
