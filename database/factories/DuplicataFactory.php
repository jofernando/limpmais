<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Fornecedor;
use App\Models\Motorista;
use App\Models\Produto;
use App\Models\Veiculo;
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
        $qtds = [
            'toneladas', 'sacos40', 'sacos50', 'sacos60', ];
        $numeros = [
            fake()->randomFloat(2, 10000, 15000),
            fake()->randomFloat(2, 100, 5000),
            fake()->randomFloat(2, 100, 5000),
        ];
        rsort($numeros);

        return [
            'valor' => $numeros[0],
            'vencimento' => $this->faker->dateTimeBetween('now', '+1 month'),
            'venda' => now(),
            'observacao' => $this->faker->sentence(4),
            'compra' => $numeros[1],
            'gastos' => $numeros[2],
            'motorista_id' => Motorista::factory(),
            'veiculo_id' => Veiculo::factory(),
            'cliente_id' => Cliente::factory(),
        ];
    }

    public function vencida(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'venda' => fake()->dateTimeBetween('-1 month', '-10 days'),
                'vencimento' => now()->subDays(2),
            ];
        });
    }
}
