<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nome' => fake()->name,
            'rua' => fake()->streetName,
            'celular' => fake()->cellphoneNumber,
            'cidade' => fake()->city,
            'estado' => fake()->state,
            'ponto_referencia' => fake()->sentence(3),
            'observacao' => fake()->sentence(10),
            'setor' => fake()->sentence(1),
            'cpf_cnpj' => fake()->cpf(),
        ];
    }
}
