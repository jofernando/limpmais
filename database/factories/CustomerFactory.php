<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "nome" => $this->faker->name,
            "rua" => $this->faker->streetName,
            "numero" => $this->faker->buildingNumber,
            "cidade" => $this->faker->city,
            "estado" => $this->faker->state
        ];
    }
}
