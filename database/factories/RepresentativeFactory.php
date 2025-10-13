<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Representative>
 */
class RepresentativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rep_name'          => $this->faker->name(),
            'rep_relationship'  => $this->faker->randomElement(['Mother', 'Father', 'Wife', 'Husband', 'Guardian']),
            'rep_contact'       => '09' . $this->faker->numerify('#########'),
            'rep_barangay'      => $this->faker->word(),
            'rep_address'       => $this->faker->address(),
            'rep_purok'         => (string) $this->faker->numberBetween(1, 10),
            'rep_street'        => $this->faker->streetName(),
            'rep_city'          => 'Tagum City',
            'rep_province'      => 'Davao del Norte',
        ];
    }
}
