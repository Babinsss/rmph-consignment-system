<?php

namespace Database\Factories;

use App\Enums\ItemCategory;
use App\Models\Consignor;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'ITM-'.fake()->unique()->numerify('#####'),
            'description' => fake()->words(3, true),
            'category' => fake()->randomElement([ItemCategory::Supply, ItemCategory::Drug]),
            'unit_of_measure' => fake()->randomElement(['piece', 'box', 'vial', 'ampule', 'bottle', 'roll']),
            'consignor_id' => Consignor::factory(),
            'minimum_level' => fake()->numberBetween(10, 100),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the item is a medical supply.
     */
    public function supply(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ItemCategory::Supply,
        ]);
    }

    /**
     * Indicate that the item is a drug/medicine.
     */
    public function drug(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => ItemCategory::Drug,
        ]);
    }
}
