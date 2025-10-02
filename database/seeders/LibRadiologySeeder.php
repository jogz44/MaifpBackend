<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\lib_radiology; // Ensure this path is correct

class LibRadiologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data extracted from the multiple images: [item_description, selling_price (SP), service_fee (TA)]
        // NOTE: The array structure has been corrected to prevent the "Undefined array key 0" error.
        $data = [
            // Data from the top of the price list (CT/MRI)
            ['item_description' => 'CT Scan of: Chest Pain', 'selling_price' => 6600.00, 'service_fee' => 660.00],
            ['item_description' => 'CT Scan of: Chest w/ Contrast', 'selling_price' => 7920.00, 'service_fee' => 792.00],
            ['item_description' => 'Hi-Res CT of the Chest', 'selling_price' => 7920.00, 'service_fee' => 792.00],
            ['item_description' => 'STAT CT Scan of: Chest Pain', 'selling_price' => 6600.00, 'service_fee' => 660.00],
            ['item_description' => 'STAT CT Scan of: Chest w/ Contrast', 'selling_price' => 7920.00, 'service_fee' => 792.00],
            ['item_description' => 'STAT CT Scan of: Abdominal Glandw/ Contrast (1.5 Tesla)', 'selling_price' => 15800.00, 'service_fee' => 1580.00],

            // Data from Image 2 & 4 (MRI/CT data)
            ['item_description' => 'Brain/IAC plain (1.5 Tesla)', 'selling_price' => 9600.00, 'service_fee' => 960.00],
            ['item_description' => 'Brain/Orbits plain (1.5 Tesla)', 'selling_price' => 18200.00, 'service_fee' => 1820.00],
            ['item_description' => 'Brain/C-Spine plain (1.5 Tesla)', 'selling_price' => 30600.00, 'service_fee' => 3060.00],
            ['item_description' => 'Chest Plain (1.5 Tesla)', 'selling_price' => 17000.00, 'service_fee' => 1700.00],
            ['item_description' => 'Chest w/ Contrast (1.5 Tesla)', 'selling_price' => 18500.00, 'service_fee' => 1850.00],
            ['item_description' => 'Lower Extremities-hip (both sides) plain (1.5 Tesla)', 'selling_price' => 15800.00, 'service_fee' => 1580.00],
            ['item_description' => 'Lower Extremities-hip (both sides) w/ contrast (1.5 Tesla)', 'selling_price' => 25800.00, 'service_fee' => 2580.00],
            ['item_description' => 'Lower Extremities-Ankle/foot (one side) Plain (1.5 Tesla)', 'selling_price' => 23400.00, 'service_fee' => 2340.00],
            ['item_description' => 'Lower Extremities-Ankle/foot (one side) w/ Contrast (1.5 T)', 'selling_price' => 26900.00, 'service_fee' => 2690.00],
            ['item_description' => 'Lower Extremities-Femur (both side) Plain (1.5 Tesla)', 'selling_price' => 21500.00, 'service_fee' => 2150.00],
            ['item_description' => 'Lower Extremities-Femur (both side) w/ Contrast (1.5 Tesla)', 'selling_price' => 23920.00, 'service_fee' => 2392.00],
            ['item_description' => 'Lower Extremities-Femur (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Lower Extremities-leg (both side) Plain (1.5 Tesla)', 'selling_price' => 23100.00, 'service_fee' => 2310.00],
            ['item_description' => 'Lower Extremities-leg (both side) w/ Contrast (1.5 Tesla)', 'selling_price' => 25300.00, 'service_fee' => 2530.00],
            ['item_description' => 'Lower Extremities-leg (one side) Plain (1.5 Tesla)', 'selling_price' => 11900.00, 'service_fee' => 1190.00],
            ['item_description' => 'Lower Extremities-leg (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 12900.00, 'service_fee' => 1290.00],
            ['item_description' => 'Lumbar/Pelvic Plain (1.5 Tesla)', 'selling_price' => 22000.00, 'service_fee' => 2200.00],
            ['item_description' => 'Lumbar/Pelvic w/ Contrast (1.5 Tesla)', 'selling_price' => 23500.00, 'service_fee' => 2350.00],
            ['item_description' => 'Lumbo/Pelvic w/ Contrast (1.5 Tesla)', 'selling_price' => 16500.00, 'service_fee' => 1650.00],
            ['item_description' => 'Upper Extremities-Arm (both sides) Plain (1.5 Tesla)', 'selling_price' => 21300.00, 'service_fee' => 2130.00],
            ['item_description' => 'Upper Extremities-Arm (both sides) w/ Contrast (1.5 Tesla)', 'selling_price' => 23600.00, 'service_fee' => 2360.00],
            ['item_description' => 'Upper Extremities-Arm (one side) Plain (1.5 Tesla)', 'selling_price' => 12000.00, 'service_fee' => 1200.00],
            ['item_description' => 'Upper Extremities-Arm (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Upper Extremities-Elbow (both sides) Plain (1.5 Tesla)', 'selling_price' => 23400.00, 'service_fee' => 2340.00],
            ['item_description' => 'Upper Extremities-Elbow (both sides) w/ Contrast (1.5 Tesla)', 'selling_price' => 25800.00, 'service_fee' => 2580.00],
            ['item_description' => 'Upper Extremities-Elbow (one side) Plain (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Upper Extremities-Elbow (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Upper Extremities-Forearm (both sides) Plain (1.5 Tesla)', 'selling_price' => 23400.00, 'service_fee' => 2340.00],
            ['item_description' => 'Upper Extremities-Forearm (one side) Plain (1.5 Tesla)', 'selling_price' => 12000.00, 'service_fee' => 1200.00],
            ['item_description' => 'Upper Extremities-Shoulder (both sides) Plain (1.5 Tesla)', 'selling_price' => 21300.00, 'service_fee' => 2130.00],
            ['item_description' => 'Upper Extremities-Shoulder (both sides) w/ Contrast (1.5 Tesla)', 'selling_price' => 23500.00, 'service_fee' => 2350.00],
            ['item_description' => 'Upper Extremities-Shoulder (one side) Plain (1.5 Tesla)', 'selling_price' => 12000.00, 'service_fee' => 1200.00],
            ['item_description' => 'Upper Extremities-Shoulder (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Upper Extremities-Wrist/Hand (both sides) Plain (1.5 Tesla)', 'selling_price' => 23400.00, 'service_fee' => 2340.00],
            ['item_description' => 'Upper Extremities-Wrist/Hand (both sides) w/ Contrast (1.5 Tesla)', 'selling_price' => 23400.00, 'service_fee' => 2340.00],
            ['item_description' => 'Upper Extremities-Wrist/Hand (one side) Plain (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'Upper Extremities-Wrist/Hand (one side) w/ Contrast (1.5 Tesla)', 'selling_price' => 13900.00, 'service_fee' => 1390.00],
            ['item_description' => 'ECG', 'selling_price' => 160.00, 'service_fee' => 16.00],
            ['item_description' => 'Bone Scan (Tc-99m)', 'selling_price' => 19790.00, 'service_fee' => 1979.00],
            ['item_description' => '2D-Echo Doppler (Adult)', 'selling_price' => 1815.00, 'service_fee' => 181.50],
            ['item_description' => 'Soft Tissue/Mass (Specify the area)', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Ultrasound (Prophylaxis)', 'selling_price' => 1700.00, 'service_fee' => 170.00],
            ['item_description' => 'Abdomen Crosstable Lateral', 'selling_price' => 200.00, 'service_fee' => 20.00],

            // Data from Image 3 (Radiography/X-ray data - part 1)
            ['item_description' => 'Abdomen Decubitus (one side)', 'selling_price' => 200.00, 'service_fee' => 20.00],
            ['item_description' => 'Abdomen I/S', 'selling_price' => 300.00, 'service_fee' => 30.00],
            ['item_description' => 'Abdomen both Decubitus', 'selling_price' => 300.00, 'service_fee' => 30.00],
            ['item_description' => 'Abdomen Decubitus and upright', 'selling_price' => 300.00, 'service_fee' => 30.00],
            ['item_description' => 'Abdomen, Upright/Supine views', 'selling_price' => 300.00, 'service_fee' => 30.00],
            ['item_description' => 'ALV', 'selling_price' => 231.00, 'service_fee' => 23.10],
            ['item_description' => 'ALV (Portable)', 'selling_price' => 231.00, 'service_fee' => 23.10],
            ['item_description' => 'Angiogram', 'selling_price' => 715.00, 'service_fee' => 71.50],
            ['item_description' => 'Angiography (Digital Subtraction)', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Angiography Unilateral', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'ANKLE APL (PORTABLE)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Ankle APL Left Lower Extremeties', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Ankle APL Right Lower Extremeties', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Ankle Joint', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Ankle Joint (one Side)', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Ankle Mortisis View', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Ankle Talocalcalc View only', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Babygram', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Barium Enema (Adult)', 'selling_price' => 800.00, 'service_fee' => 80.00],
            ['item_description' => 'Barium Enema (Pedia)', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Both Shoulders (Portable)', 'selling_price' => 304.00, 'service_fee' => 30.40],
            ['item_description' => 'Cardiac Sereis', 'selling_price' => 230.00, 'service_fee' => 23.00],
            ['item_description' => 'Cervical APL', 'selling_price' => 355.00, 'service_fee' => 35.50],
            ['item_description' => 'Cervical Spine (4 views)', 'selling_price' => 527.00, 'service_fee' => 52.70],
            ['item_description' => 'Cervical Spines AP-Lateral', 'selling_price' => 355.00, 'service_fee' => 35.50],
            ['item_description' => 'Cervical Spines AP-Lateral Oblique', 'selling_price' => 270.00, 'service_fee' => 27.00],
            ['item_description' => 'Check Film', 'selling_price' => 0.00, 'service_fee' => 0.00],
            ['item_description' => 'Chest AP/PA Portable', 'selling_price' => 238.00, 'service_fee' => 23.80],
            ['item_description' => 'Chest APL (Child)', 'selling_price' => 348.00, 'service_fee' => 34.80],
            ['item_description' => 'Chest Lateral Decub Left', 'selling_price' => 231.00, 'service_fee' => 23.10],
            ['item_description' => 'Chest Lateral Decub Right', 'selling_price' => 231.00, 'service_fee' => 23.10],
            ['item_description' => 'Chest Lateral Decub(Portable)', 'selling_price' => 250.00, 'service_fee' => 25.00],
            ['item_description' => 'Chest AP/PA(Adult)', 'selling_price' => 231.00, 'service_fee' => 23.10],

            // Data from Image 6 (Radiography/X-ray data - part 2)
            ['item_description' => 'Chest PAL/AP ADULT', 'selling_price' => 348.00, 'service_fee' => 34.80],
            ['item_description' => 'Chest PAL ADULT (PORTABLE)', 'selling_price' => 348.00, 'service_fee' => 34.80],
            ['item_description' => 'Chest PA Lateral (PEDIA)', 'selling_price' => 250.00, 'service_fee' => 25.00],
            ['item_description' => 'Chole GI Series', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Clavicle APL', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Clavicle APL (Portable)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Clavicle APL Left Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Clavicle APL Right Upper Extremities', 'selling_price' => 419.00, 'service_fee' => 41.90],
            ['item_description' => 'Close Reduction Procedures', 'selling_price' => 715.00, 'service_fee' => 71.50],
            ['item_description' => 'EFM (Electronic Fetal Monitoring)', 'selling_price' => 250.00, 'service_fee' => 25.00],
            ['item_description' => 'Elbow APL', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Elbow APL (Portable)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Elbow APL Left Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Elbow APL Right Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Esophagogram', 'selling_price' => 715.00, 'service_fee' => 71.50],

            // Data from Image 5 (Radiography/X-ray data - part 3)
            ['item_description' => 'Esophagography/Barium Swallow', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Facial Bone', 'selling_price' => 270.00, 'service_fee' => 27.00],
            ['item_description' => 'Femur APL', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Femur APL Left Lower Extremities', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Femur APL Right Lower Extremities', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Femur of Inigh', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Fistologram', 'selling_price' => 200.00, 'service_fee' => 20.00],
            ['item_description' => 'Fluoroscopic', 'selling_price' => 715.00, 'service_fee' => 71.50],
            ['item_description' => 'Fluoroscopic Guided', 'selling_price' => 37190.00, 'service_fee' => 3719.00],
            ['item_description' => 'Foot APL (PORTABLE)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Foot APO Left Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Foot APO Right Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Foot Oblique View', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Forearm APL (Portable)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Forearm APL (Left Upper Extremities)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Forearm APL (Right Upper Extremities)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Hand APL (Portable)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Hand APL Left Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Hand APL Right Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Hand Lateral View', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Hip AP/CT/APL', 'selling_price' => 175.00, 'service_fee' => 17.50],
            ['item_description' => 'Hip Joint AP', 'selling_price' => 175.00, 'service_fee' => 17.50],
            ['item_description' => 'Humerus Forearm/Arm', 'selling_price' => 185.00, 'service_fee' => 18.50],
            ['item_description' => 'Humerus APL (Portable)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Humerus APL Left Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Humerus APL Right Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Hystero-Intravenous BF', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Hysterosalpingodenography', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Hysterosalpingography', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'Intra-op Procedures (part xrayd plus this amount)', 'selling_price' => 650.00, 'service_fee' => 65.00],
            ['item_description' => 'IVP', 'selling_price' => 710.00, 'service_fee' => 71.00],
            ['item_description' => 'Knee APL (PORTABLE)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Knee APL Left Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Knee APL Right Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Knee APL Right Upper Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'KUB', 'selling_price' => 200.00, 'service_fee' => 20.00],
            ['item_description' => 'KUB (Bowel Prep. Needed)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Leg', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Leg (PORTABLE)', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Leg Left Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Leg Right Lower Extremities', 'selling_price' => 283.00, 'service_fee' => 28.30],
            ['item_description' => 'Lumbar Vertebrae AP L', 'selling_price' => 270.00, 'service_fee' => 27.00],
            ['item_description' => 'Lumbar Vertebrae AP L Oblique', 'selling_price' => 270.00, 'service_fee' => 27.00],
            ['item_description' => 'Lumbo Sacro APL', 'selling_price' => 422.00, 'service_fee' => 42.20],
            ['item_description' => 'Lumbosacral APL (4 views)', 'selling_price' => 603.00, 'service_fee' => 60.30],
            ['item_description' => 'Lumbosacral Spine + Obliques', 'selling_price' => 400.00, 'service_fee' => 40.00],
            ['item_description' => 'Lumbosacral Spine APL', 'selling_price' => 422.00, 'service_fee' => 42.20],
            ['item_description' => 'Mandible APL', 'selling_price' => 312.00, 'service_fee' => 31.20],
            ['item_description' => 'Mastoids Series', 'selling_price' => 351.00, 'service_fee' => 35.10],
            ['item_description' => 'Maxillae', 'selling_price' => 250.00, 'service_fee' => 25.00],
            ['item_description' => 'Myelography', 'selling_price' => 650.00, 'service_fee' => 65.00],
        ];

        // Clear existing data to prevent duplicates on reruns
        lib_radiology::truncate();

        foreach ($data as $item) {
            $record = [
                'item_description' => $item['item_description'],
                'selling_price'    => $item['selling_price'],
                'service_fee'      => $item['service_fee'],
            ];

            // Calculate total_amount: selling_price + service_fee
            $record['total_amount'] = $record['selling_price'] + $record['service_fee'];

            lib_radiology::create($record);
        }
    }
}
