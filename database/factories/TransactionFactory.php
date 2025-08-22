<?php

namespace Database\Factories;


use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'transaction_type' => $this->faker->randomElement(['Consultation', 'Medication', 'Laboratory']),
            'transaction_date' => now()->toDateString(), // ✅ always today
            'transaction_mode' => $this->faker->randomElement(['Walk-in', 'Online', 'Referral']),
            'purpose' => $this->faker->sentence(3),
            'transaction_number' => $this->faker->unique()->numerify('TRX-#####'),
        ];
    }
}
