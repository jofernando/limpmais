<?php

namespace Database\Factories;

use App\Models\MetodoPagamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fornecedor>
 */
class FornecedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cpf_cnpj' => fake()->cnpj,
            'empresa' => fake()->company,
        ];
    }
}
