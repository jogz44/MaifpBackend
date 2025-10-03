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
        // Data extracted from the image: [item_id, item_description, selling_price (CP), service_fee (TA)]
        $data = [
            ['LAB000031', 'Albumin Serum', 155.00, 15.50],

            ['LAB000083', 'Anti-HBS, qualitative', 250.00, 25.00],

            ['LAB000034', 'Aspartate aminotransferase (AST/SGOT)', 185.00, 18.50],

            ['LAB000414', 'Bleeding Time (Manual)', 75.00, 7.50],

            ['LAB000097', 'Blood Extraction Fee', 40.00, 4.00],

            ['LAB000008', 'Blood Indices (MCV,MCH,MCHC)', 185.00, 18.50],

            ['LAB000009', 'Blood Typing (ABO/Rh)', 62.00, 6.20],

            ['LAB000036', 'Blood Urea Nitrogen (BUN)', 185.00, 18.50],

            ['LAB000303', 'CBC with Platelet Count', 190.00, 19.00],

            ['LAB000037', 'Cholesterol', 184.00, 18.40],

            ['LAB000038', 'CK-MB', 500.00, 50.00],

            ['LAB000412', 'Clotting Time (Manual)', 75.00, 7.50],

            ['LAB000504', 'Creatinine Serum', 150.00, 15.00],

            ['LAB000087', 'Dengue Duo (NS1 Antibody)', 711.00, 71.10],

            ['LAB000501', 'Electrolytes Serum (Na,K,Cl)', 434.00, 43.40],

            ['LAB000407', 'Electrolytes, Urine (Na+, K+, Cl-)', 350.00, 35.00],

            ['LAB000011', 'ERYTHROCYTE SEDIMENTATION RATE (ESR)', 130.00, 13.00],

            ['LAB000099', 'Expanded Newborn Screening (ENBS)', 1800.00, 180.00],

            ['LAB000040', 'Fasting Blood Sugar (FBS) (Glucose)', 110.00, 11.00],

            ['LAB000021', 'Fecal Occult Blood Test (FOBT)', 110.00, 11.00],

            ['LAB000019', 'Fecalysis', 50.00, 5.00],

            ['LAB000415', 'Fecalysis, Kato, Katz', 135.00, 13.50],

            ['LAB000138', 'Glucose Tolerance (OGTT)', 893.00, 89.30],

            ['LAB000505', 'Glucose, Random', 131.00, 13.10],

            ['LAB000069', 'Helicobacter pylori (H.pylori), Stool', 425.00, 42.50],

            ['LAB000002', 'Hematocrit', 150.00, 15.00],
            ['LAB000003', 'Hemoglobin', 150.00, 15.00],
            ['LAB000042', 'Lipid Profile (Cholesterol, HDL, LDL)', 600.00, 60.00],

            ['LAB000004', 'Platelet Count', 100.00, 10.00],

            ['LAB000022', 'Pregnancy Test', 110.00, 11.00],

            ['LAB000007', 'RBC Count', 150.00, 15.00],

            ['LAB000014', 'RETICULOCYTE COUNT', 306.00, 30.60],

            ['LAB000086', 'Syphilis, qualitative', 223.00, 22.30],
            ['LAB000049', 'Triglycerides', 220.00, 22.00],
            ['LAB000071', 'TROPONIN I (qualitative)', 350.00, 35.00],

            ['LAB000093', 'Typhoid IgG/IgM,qualitative', 850.00, 85.00],

            ['LAB000046', 'Uric Acid', 184.00, 18.40],

            ['LAB000024', 'Urinalysis Automated (10 Parameters)', 115.00, 11.50],
            ['LAB000025', 'Urine Albumin (Qualitative)', 40.00, 4.00],
            ['LAB000433', 'Urine Ketone', 40.00, 4.00],

            ['LAB000029', 'Urine PH', 40.00, 4.00],
            ['LAB000028', 'Urine Specific Gravity', 40.00, 4.00],
            ['LAB000027', 'Urine Sugar', 40.00, 4.00],
            ['LAB000006', 'WBC Count', 150.00, 15.00],
        ];

        // // Clear existing data to prevent duplicates on reruns
        Lib_lab_examination::truncate();

        // Process and insert the data
        foreach ($data as $item) {
            $record = [
                'item_id'          => $item[0],
                'item_description' => $item[1],
                'selling_price'    => $item[2], // CP from the image
                'service_fee'      => $item[3], // TA from the image
            ];

            // Calculate total_amount: selling_price + service_fee
            $record['total_amount'] = $record['selling_price'] + $record['service_fee'];

            Lib_lab_examination::create($record);
        }
    }
}
