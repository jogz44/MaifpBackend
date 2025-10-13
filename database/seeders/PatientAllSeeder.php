<?php

namespace Database\Seeders;

use App\Models\vital;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\Representative;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PatientAllSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate 10 random records
        for ($i = 0; $i < 10; $i++) {

            $patient = Patient::factory()->create();
            $representative = Representative::factory()->create();

            // Generate transaction number
            $datePart = now()->format('Y-m-d');
            $sequenceFormatted = str_pad($patient->id, 5, '0', STR_PAD_LEFT);
            $transactionNumber = "{$datePart}-{$sequenceFormatted}";

            $philhealth = $patient->philhealth_id ? true : false;
            $maifip = !$philhealth;

            $transaction = Transaction::create([
                'patient_id'          => $patient->id,
                'representative_id'   => $representative->id,
                'transaction_number'  => $transactionNumber,
                'transaction_type'    => 'Consultation',
                'transaction_date'    => now(),
                'transaction_mode'    => 'Walk-in',
                'purpose'             => 'Medical check-up',
                'philhealth'          => $philhealth,
                'maifip'              => $maifip,
            ]);

            $vital = vital::factory()->create([
                'patient_id'     => $patient->id,
                'transaction_id' => $transaction->id,
            ]);

            $this->command->info("✅ Patient {$patient->firstname} {$patient->lastname} with transaction {$transactionNumber} created.");
        }
    }
}
