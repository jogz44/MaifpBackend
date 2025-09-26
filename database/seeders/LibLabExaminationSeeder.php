<?php

namespace Database\Seeders;

use App\Models\Lib_lab_examination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibLabExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $items = [
            ['item_id' => 'LAB000031', 'item_description' => 'Albumin Serum', 'service_fee' => 155.00, 'total_amount' => 155.00, 'selling_price' => 15.50],
            ['item_id' => 'LAB000033', 'item_description' => 'Anti-HCV, qualitative', 'service_fee' => 785.00, 'total_amount' => 785.00, 'selling_price' => 15.50],
            ['item_id' => 'LAB000034', 'item_description' => 'Aspartate Aminotransferase (AST/SGOT)', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 15.50],
            ['item_id' => 'LAB000035', 'item_description' => 'Bleeding Time (Manual)', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000036', 'item_description' => 'Blood Extraction Fee', 'service_fee' => 70.00, 'total_amount' => 70.00, 'selling_price' => 7.00],
            ['item_id' => 'LAB000037', 'item_description' => 'Blood Indices (MCV,MCH,MCHC)', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000038', 'item_description' => 'Blood Typing (ABO/Rh)', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000039', 'item_description' => 'Blood Urea Nitrogen (BUN)', 'service_fee' => 195.00, 'total_amount' => 195.00, 'selling_price' => 19.50],
            ['item_id' => 'LAB000040', 'item_description' => 'CBC with Platelet Count', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000041', 'item_description' => 'Cholesterol', 'service_fee' => 165.00, 'total_amount' => 165.00, 'selling_price' => 16.50],
            ['item_id' => 'LAB000042', 'item_description' => 'CK-MB', 'service_fee' => 820.00, 'total_amount' => 820.00, 'selling_price' => 82.00],
            ['item_id' => 'LAB000043', 'item_description' => 'Clotting Time (Manual)', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000044', 'item_description' => 'Creatinine Serum', 'service_fee' => 175.00, 'total_amount' => 175.00, 'selling_price' => 17.50],
            ['item_id' => 'LAB000045', 'item_description' => 'CRP (C-Reactive Protein, Antibody)', 'service_fee' => 715.00, 'total_amount' => 715.00, 'selling_price' => 71.50],
            ['item_id' => 'LAB000046', 'item_description' => 'Electrolytes Serum (Na,K,Cl)', 'service_fee' => 430.00, 'total_amount' => 430.00, 'selling_price' => 43.00],
            ['item_id' => 'LAB000047', 'item_description' => 'Erythrocyte Sedimentation Rate (ESR)', 'service_fee' => 95.00, 'total_amount' => 95.00, 'selling_price' => 9.50],
            ['item_id' => 'LAB000048', 'item_description' => 'Expanded Newborn Screening (ENBS)', 'service_fee' => 1180.00, 'total_amount' => 1180.00, 'selling_price' => 118.00],
            ['item_id' => 'LAB000049', 'item_description' => 'Fasting Blood Sugar (FBS)/Glucose', 'service_fee' => 110.00, 'total_amount' => 110.00, 'selling_price' => 11.00],
            ['item_id' => 'LAB000050', 'item_description' => 'Fecal Occult Blood Test (FOBT)', 'service_fee' => 150.00, 'total_amount' => 150.00, 'selling_price' => 15.00],
            ['item_id' => 'LAB000051', 'item_description' => 'Ferritin', 'service_fee' => 935.00, 'total_amount' => 935.00, 'selling_price' => 93.50],
            ['item_id' => 'LAB000052', 'item_description' => 'Fractional Excretion', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000053', 'item_description' => 'Fecaly, Kato, Katz', 'service_fee' => 135.00, 'total_amount' => 135.00, 'selling_price' => 13.50],
            ['item_id' => 'LAB000054', 'item_description' => 'Gamma Aspartase (OGT)', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000055', 'item_description' => 'Glucose, Random', 'service_fee' => 110.00, 'total_amount' => 110.00, 'selling_price' => 11.00],
            ['item_id' => 'LAB000056', 'item_description' => 'Hemoglobin', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000057', 'item_description' => 'Helicobacter pylori (H.pylori), Stool', 'service_fee' => 450.00, 'total_amount' => 450.00, 'selling_price' => 45.00],
            ['item_id' => 'LAB000058', 'item_description' => 'Hematocrit', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000059', 'item_description' => 'HIV Profile', 'service_fee' => 1000.00, 'total_amount' => 1000.00, 'selling_price' => 100.00],
            ['item_id' => 'LAB000060', 'item_description' => 'Lipid Profile (Cholesterol, HDL, LDL)', 'service_fee' => 600.00, 'total_amount' => 600.00, 'selling_price' => 60.00],
            ['item_id' => 'LAB000061', 'item_description' => 'Malaria Parasite', 'service_fee' => 100.00, 'total_amount' => 100.00, 'selling_price' => 10.00],
            ['item_id' => 'LAB000062', 'item_description' => 'Platelet Count', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000063', 'item_description' => 'Pregnant Test', 'service_fee' => 135.00, 'total_amount' => 135.00, 'selling_price' => 13.50],
            ['item_id' => 'LAB000064', 'item_description' => 'RBC COUNT', 'service_fee' => 125.00, 'total_amount' => 125.00, 'selling_price' => 12.50],
            ['item_id' => 'LAB000065', 'item_description' => 'RETICULOCYTE COUNT', 'service_fee' => 220.00, 'total_amount' => 220.00, 'selling_price' => 22.00],
            ['item_id' => 'LAB000066', 'item_description' => 'Syphilis, qualitative', 'service_fee' => 320.00, 'total_amount' => 320.00, 'selling_price' => 32.00],
            ['item_id' => 'LAB000067', 'item_description' => 'Triglycerides', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000068', 'item_description' => 'TROPONIN I (qualitative)', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000069', 'item_description' => 'Typhoid IgG/IgM,qualitative', 'service_fee' => 185.00, 'total_amount' => 185.00, 'selling_price' => 18.50],
            ['item_id' => 'LAB000070', 'item_description' => 'Urine Acid Automated (10 Parameters)', 'service_fee' => 40.00, 'total_amount' => 40.00, 'selling_price' => 4.00],
            ['item_id' => 'LAB000071', 'item_description' => 'Urine Albumin (Qualitative)', 'service_fee' => 40.00, 'total_amount' => 40.00, 'selling_price' => 4.00],
            ['item_id' => 'LAB000072', 'item_description' => 'Urine PH', 'service_fee' => 40.00, 'total_amount' => 40.00, 'selling_price' => 4.00],
            ['item_id' => 'LAB000073', 'item_description' => 'Urine Specific Gravity', 'service_fee' => 40.00, 'total_amount' => 40.00, 'selling_price' => 4.00],
            ['item_id' => 'LAB000074', 'item_description' => 'Urine Sugar', 'service_fee' => 40.00, 'total_amount' => 40.00, 'selling_price' => 4.00],
            ['item_id' => 'LAB000075', 'item_description' => 'WBC Count', 'service_fee' => 150.00, 'total_amount' => 150.00, 'selling_price' => 15.00],
        ];

        foreach ($items as $item) {
            Lib_lab_examination::create($item);
        }
    }
}
