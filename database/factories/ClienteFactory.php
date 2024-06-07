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
            "nome" => $this->faker->name,
            "rua" => $this->faker->streetName,
            "celular" => $this->faker->cellphoneNumber,
            "cidade" => $this->faker->city,
            "estado" => $this->faker->state,
            "ponto_referencia" => $this->faker->sentence(3),
            'observacao' => $this->faker->sentence(10),
            'setor' => $this->faker->sentence(1),
            'cpf_cnpj' => $this->faker->cpf(),
        ];
    }
}
