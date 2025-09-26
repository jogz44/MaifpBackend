<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibUltraSoundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lib_ultra_sound')->insert([
            [
                'body_parts' => 'Whole Abdomen',
                'rate' => 900.00,
                'service_fee' => 90.00,
                'total_amount' => 90.00
            ],
            [
                'body_parts' => 'Whole Abdomen/Prostate',
                'rate' => 980.00,
                'service_fee' => 98.00,
                'total_amount' => 98.00
            ],
            [
                'body_parts' => 'Whole Abdomen/Pelvis',
                'rate' => 900.00,
                'service_fee' => 90.00,
                'total_amount' => 90.00
            ],
            [
                'body_parts' => 'Upper Abdomen',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Lower Abdomen',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'HBT/Pancreas/Spleen',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'HBT/Pancreas',
                'rate' => 450.00,
                'service_fee' => 45.00,
                'total_amount' => 45.00
            ],
            [
                'body_parts' => 'HBT',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'body_parts' => 'Liver',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'body_parts' => 'Spleen',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'body_parts' => 'Pancreas',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'body_parts' => 'Kub',
                'rate' => 500.00,
                'service_fee' => 50.00,
                'total_amount' => 50.00
            ],
            [
                'body_parts' => 'Prostate',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Kub/Prostate',
                'rate' => 800.00,
                'service_fee' => 80.00,
                'total_amount' => 80.00
            ],
            [
                'body_parts' => 'Pelvis',
                'rate' => 1200.00,
                'service_fee' => 120.00,
                'total_amount' => 120.00
            ],
            [
                'body_parts' => 'Scrotum',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Inguinal-Scrotal',
                'rate' => 1000.00,
                'service_fee' => 100.00,
                'total_amount' => 100.00
            ],
            [
                'body_parts' => 'Both inguinal Area',
                'rate' => 1000.00,
                'service_fee' => 100.00,
                'total_amount' => 100.00
            ],
            [
                'body_parts' => 'Inguinal Area [ Right ] [ Left ]',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Both Hemothorax',
                'rate' => 1000.00,
                'service_fee' => 100.00,
                'total_amount' => 100.00
            ],
            [
                'body_parts' => 'Hemothorax [ Right ] [ Left ]',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Cranial',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Thyroid',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Breast',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Para-Aortic Nodes',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Soft Tissue / Mass (specify the Area)',
                'rate' => 650.00,
                'service_fee' => 65.00,
                'total_amount' => 65.00
            ],
            [
                'body_parts' => 'Doppler, Upper Ext, Per Limb',
                'rate' => 1800.00,
                'service_fee' => 180.00,
                'total_amount' => 180.00
            ],
            [
                'body_parts' => 'Carotid Doppler',
                'rate' => 2000.00,
                'service_fee' => 200.00,
                'total_amount' => 200.00
            ],
            [
                'body_parts' => 'Renal Graft',
                'rate' => 2500.00,
                'service_fee' => 250.00,
                'total_amount' => 250.00
            ],
            [
                'body_parts' => 'Renal Doppler',
                'rate' => 2500.00,
                'service_fee' => 250.00,
                'total_amount' => 250.00
            ],
        ]);
    }
}
