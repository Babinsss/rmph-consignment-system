<?php

namespace Database\Factories;

use App\Models\Consignor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consignor>
 */
class ConsignorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company().' Medical Supply Corp',
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'contact_person' => fake()->name(),
            'contact_number' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
