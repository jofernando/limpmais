<?php

namespace Database\Factories;

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
        $numeros = [
            fake()->randomFloat(2, 10000, 15000),
            fake()->randomFloat(2, 100, 5000),
            fake()->randomFloat(2, 100, 5000),
        ];
        rsort($numeros);

        return [
            'valor' => $numeros[0],
            'vencimento' => fake()->dateTimeBetween('now', '+1 month'),
            'venda' => now(),
            'observacao' => fake()->sentence(4),
            'compra' => $numeros[1],
            'gastos' => $numeros[2],
            'produto_id' => Produto::factory(),
            'fornecedor_id' => Fornecedor::factory(),
            'motorista_id' => Motorista::factory(),
            'veiculo_id' => Veiculo::factory(),
        ];
    }
}
