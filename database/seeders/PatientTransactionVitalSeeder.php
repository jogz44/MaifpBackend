<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\Vital;

class PatientTransactionVitalSeeder extends Seeder
{
    public function run(): void
    {
        // create 20 patients with transactions and vitals
        Patient::factory(10)->create()->each(function ($patient) {
            $transaction = Transaction::factory()->create([
                'patient_id' => $patient->id,
                'transaction_number' => now()->format('Y-m-d') . '-' . str_pad($patient->id, 5, '0', STR_PAD_LEFT),
            ]);

            Vital::factory()->create([
                'patient_id' => $patient->id,
                'transaction_id' => $transaction->id,
            ]);
        });
    }
}
