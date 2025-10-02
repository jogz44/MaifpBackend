<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\lib_laboratory;

class LibLaboratorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $labs = [
            // ['lab_name' => 'Blood Smear for malarial Parasites (BSMP)', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Blood Typing', 'lab_amount' => 70.00, 'service_fee' => 7.00, 'total_amount' => 77.00],
            // ['lab_name' => 'Complete Blood Count (CBC)', 'lab_amount' => 100.00, 'service_fee' => 10.00, 'total_amount' => 110.00],
            // ['lab_name' => 'Fecalysis (Stool Exams)', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Gram Stain (Uretral & Vaginal Smears)', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Hematocrit', 'lab_amount' => 70.00, 'service_fee' => 7.00, 'total_amount' => 77.00],
            // ['lab_name' => 'Hemoglobin Determination', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'KatoKatz', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Occult Blood (Stool)', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'PAP Smear', 'lab_amount' => 100.00, 'service_fee' => 10.00, 'total_amount' => 110.00],
            // ['lab_name' => 'Platelet Count', 'lab_amount' => 70.00, 'service_fee' => 7.00, 'total_amount' => 77.00],
            // ['lab_name' => 'Pregnancy Test', 'lab_amount' => 50.00, 'service_fee' => 5.00, 'total_amount' => 55.00],
            // ['lab_name' => 'Prothrombin Time', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Sputum Examination', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'Urinalysis', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
            // ['lab_name' => 'VDRL', 'lab_amount' => 30.00, 'service_fee' => 3.00, 'total_amount' => 33.00],
        ];

        foreach ($labs as $lab) {
            lib_laboratory::create($lab);
        }
    }
}
