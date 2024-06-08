<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Duplicata;
use App\Models\MetodoPagamento;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $credcards = MetodoPagamento::factory()->count(2)->create();
        Cliente::factory()->has(
            Duplicata::factory()->count(3)->has(
                Pagamento::factory()->count(2)->state(
                    function (array $attributes, Duplicata $duplicata) use ($credcards) {
                        return [
                            'valor' => $duplicata->valor / 3,
                            'metodo_pagamento_id' => $credcards->random(1)->first()->id,
                        ];
                    }
                )
            )
        )->count(30)->create();
        Cliente::factory()->count(8)->create();
        User::factory()->create();
    }
}
