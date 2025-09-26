<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LibRadiologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lib_radiology')->insert([
            [
                'item_description' => 'Abdomen Decubitus (one side)',
                'service_fee' => 200.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'Abdomen U/S',
                'service_fee' => 300.00,
                'total_amount' => 50.00,
                'selling_price' => 50.00
            ],
            [
                'item_description' => 'Abdomen both Decubitus',
                'service_fee' => 200.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'Abdomen Decubitus and upright',
                'service_fee' => 200.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'Abdomen, Upright/Supine views',
                'service_fee' => 200.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'ALV',
                'service_fee' => 231.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'ALV (Portable)',
                'service_fee' => 731.00,
                'total_amount' => 30.00,
                'selling_price' => 30.00
            ],
            [
                'item_description' => 'Angiography (Digital Subtraction)',
                'service_fee' => 715.00,
                'total_amount' => 60.00,
                'selling_price' => 60.00
            ],
            [
                'item_description' => 'Angiography Unilateral',
                'service_fee' => 300.00,
                'total_amount' => 50.00,
                'selling_price' => 50.00
            ],
            [
                'item_description' => 'ANKLE APL (PORTABLE)',
                'service_fee' => 238.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Ankle APL Left Lower Extremities',
                'service_fee' => 238.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Ankle APL Right Lower Extremities',
                'service_fee' => 238.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Ankle Joint',
                'service_fee' => 165.00,
                'total_amount' => 15.00,
                'selling_price' => 15.00
            ],
            [
                'item_description' => 'Ankle Joint (one side)',
                'service_fee' => 185.00,
                'total_amount' => 15.00,
                'selling_price' => 15.00
            ],
            [
                'item_description' => 'Ankle Mortise View',
                'service_fee' => 185.00,
                'total_amount' => 15.00,
                'selling_price' => 15.00
            ],
            [
                'item_description' => 'Abdicatoric View only',
                'service_fee' => 165.00,
                'total_amount' => 15.00,
                'selling_price' => 15.00
            ],
            [
                'item_description' => 'Babygram',
                'service_fee' => 600.00,
                'total_amount' => 60.00,
                'selling_price' => 60.00
            ],
            [
                'item_description' => 'Barium Enema (Adult)',
                'service_fee' => 560.00,
                'total_amount' => 55.00,
                'selling_price' => 55.00
            ],
            [
                'item_description' => 'Barium Enema (Pedia)',
                'service_fee' => 400.00,
                'total_amount' => 40.00,
                'selling_price' => 40.00
            ],
            [
                'item_description' => 'Both Shoulders (Portable)',
                'service_fee' => 377.00,
                'total_amount' => 37.00,
                'selling_price' => 37.00
            ],
            [
                'item_description' => 'Cardiac Series',
                'service_fee' => 300.00,
                'total_amount' => 27.00,
                'selling_price' => 27.00
            ],
            [
                'item_description' => 'Cervical APL',
                'service_fee' => 238.00,
                'total_amount' => 27.00,
                'selling_price' => 27.00
            ],
            [
                'item_description' => 'Cervical Spine (4 views)',
                'service_fee' => 348.00,
                'total_amount' => 34.00,
                'selling_price' => 34.00
            ],
            [
                'item_description' => 'Cervical Spines AP-Lateral',
                'service_fee' => 238.00,
                'total_amount' => 27.00,
                'selling_price' => 27.00
            ],
            [
                'item_description' => 'Cervical Spines AP-Lateral Oblique',
                'service_fee' => 238.00,
                'total_amount' => 27.00,
                'selling_price' => 27.00
            ],
            [
                'item_description' => 'Check Film',
                'service_fee' => 100.00,
                'total_amount' => 10.00,
                'selling_price' => 10.00
            ],
            [
                'item_description' => 'Chest AP/PA (Pedia)',
                'service_fee' => 238.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Chest AP/PA-Lateral',
                'service_fee' => 348.00,
                'total_amount' => 34.00,
                'selling_price' => 34.00
            ],
            [
                'item_description' => 'Chest APL (Child)',
                'service_fee' => 238.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Chest Lateral Decub Left',
                'service_fee' => 250.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Chest Lateral Decub Right',
                'service_fee' => 250.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
            [
                'item_description' => 'Chest Lateral Decub(Portable)',
                'service_fee' => 250.00,
                'total_amount' => 25.00,
                'selling_price' => 25.00
            ],
        ]);
    }
}
