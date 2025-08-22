<?php

namespace Database\Factories;

use App\Models\Vital;
use App\Models\Patient;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class VitalFactory extends Factory
{
    protected $model = Vital::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'transaction_id' => Transaction::factory(),
            'height' => $this->faker->numberBetween(140, 190) . ' cm',
            'weight' => $this->faker->numberBetween(40, 100) . ' kg',
            'bmi' => $this->faker->randomFloat(1, 15, 35),
            'temperature' => $this->faker->randomFloat(1, 35, 40) . ' °C',
            'waist' => $this->faker->numberBetween(60, 120) . ' cm',
            'pulse_rate' => $this->faker->numberBetween(60, 120),
            'sp02' => $this->faker->numberBetween(90, 100) . '%',
            'heart_rate' => $this->faker->numberBetween(60, 120),
            'blood_pressure' => $this->faker->numberBetween(90, 140) . '/' . $this->faker->numberBetween(60, 90),
            'respiratory_rate' => $this->faker->numberBetween(12, 20),
            'medicine' => $this->faker->optional()->word,
            'LMP' => $this->faker->optional()->date('Y-m-d'),
        ];
    }
}
