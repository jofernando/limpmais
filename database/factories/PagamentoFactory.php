<?php

namespace Database\Factories;

use App\Models\Duplicata;
use App\Models\MetodoPagamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pagamento>
 */
class PagamentoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'valor' => fake()->randomFloat(2),
            'data' => now(),
            'metodo_pagamento_id' => MetodoPagamento::factory(),
            'duplicata_id' => Duplicata::factory(),
        ];
    }
}
