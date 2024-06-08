<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Duplicata>
 */
class DuplicataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $numeros = [
            $this->faker->randomFloat(2),
            $this->faker->randomFloat(2),
            $this->faker->randomFloat(2),
        ];
        rsort($numeros);

        return [
            'valor' => $numeros[0],
            'vencimento' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'observacao' => $this->faker->sentence(4),
            'compra' => $numeros[1],
            'gastos' => $numeros[2],
        ];
    }
}
