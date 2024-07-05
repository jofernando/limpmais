<?php

namespace Database\Factories;

use App\Models\Duplicata;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
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
            'produto_id' => Produto::factory(),
            'fornecedor_id' => Fornecedor::factory(),
            'duplicata_id' => Duplicata::factory(),
            'tipo_quantidade' => fake()->randomElement(['toneladas', 'sacos40', 'sacos50', 'sacos60', 'unidades']),
            'quantidade' => fake()->randomNumber(4),
        ];
    }
}
