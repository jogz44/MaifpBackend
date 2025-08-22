<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'middlename' => $this->faker->optional()->firstName,
            'ext' => $this->faker->optional()->suffix,
            'birthdate' => $this->faker->date('Y-m-d', '-18 years'),
            'contact_number' => $this->faker->numerify('09#########'),
            'age' => $this->faker->numberBetween(1, 90),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'is_not_tagum' => $this->faker->boolean,
            'street' => $this->faker->streetName,
            'purok' => $this->faker->word,
            'barangay' => $this->faker->citySuffix,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'category' => $this->faker->randomElement(['Child', 'Adult', 'Senior']),
            'is_pwd' => $this->faker->boolean,
            'is_solo' => $this->faker->boolean,
            // 'user_id' => User::factory(), // random user
        ];
    }
}
