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
            'firstname'          => $this->faker->firstName(),
            'lastname'           => $this->faker->lastName(),
            'middlename'         => $this->faker->firstName(),
            'ext'                => $this->faker->optional()->randomElement(['Jr.', 'Sr.', null]),
            'birthdate'          => $this->faker->date('Y-m-d', '-20 years'),
            'contact_number'     => '09' . $this->faker->numerify('#########'),
            'age'                => $this->faker->numberBetween(1, 90),
            'gender'             => $this->faker->randomElement(['Male', 'Female']),
            'is_not_tagum'       => $this->faker->boolean(),
            'street'             => $this->faker->streetName(),
            'purok'              => (string) $this->faker->numberBetween(1, 10),
            'barangay'           => $this->faker->word(),
            'city'               => 'Tagum City',
            'province'           => 'Davao del Norte',
            'category'           => $this->faker->randomElement(['Adult', 'Child', 'Senior']),
            'philsys_id'         => $this->faker->optional()->numerify('############'),
            'philhealth_id'      => $this->faker->optional()->numerify('###########'),
            'place_of_birth'     => $this->faker->city(),
            'civil_status'       => $this->faker->randomElement(['Single', 'Married', 'Widowed']),
            'religion'           => $this->faker->randomElement(['Catholic', 'Christian', 'Muslim']),
            'education'          => $this->faker->randomElement(['Elementary', 'High School', 'College Graduate']),
            'occupation'         => $this->faker->jobTitle(),
            'income'             => $this->faker->numberBetween(5000, 30000),
            'is_pwd'             => $this->faker->boolean(),
            'is_solo'            => $this->faker->boolean(),
            'permanent_street'   => $this->faker->streetName(),
            'permanent_purok'    => (string) $this->faker->numberBetween(1, 10),
            'permanent_barangay' => $this->faker->word(),
            'permanent_city'     => 'Tagum City',
            'permanent_province' => 'Davao del Norte',
        ];
    }
}
