<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Duplicata;
use App\Models\Fornecedor;
use App\Models\Item;
use App\Models\MetodoPagamento;
use App\Models\Motorista;
use App\Models\Pagamento;
use App\Models\Produto;
use App\Models\User;
use App\Models\Cor;
use App\Models\Tamanho;
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
        $fornecedores = Fornecedor::factory()->count(3)->create();
        $produtos = Produto::factory()->count(3)->create();
        $cores = Cor::factory()->count(5)->create();
        $tamanhos = Tamanho::factory()->count(5)->create();
        Cliente::factory()->has(
            Duplicata::factory()
                ->count(3)
                ->recycle($produtos)
                ->recycle($fornecedores)
                ->hasPagamentos(2, function (array $attributes, Duplicata $duplicata) use ($credcards) {
                    return [
                        'valor' => $duplicata->valor / 3,
                        'metodo_pagamento_id' => $credcards->random(1)->first()->id,
                    ];
                })
                ->hasItens(2, function (array $attributes, Duplicata $duplicata) use ($cores, $tamanhos) {
                    return [
                        'cor_id' => $cores->random(1)->first()->id,
                        'tamanho_id' => $tamanhos->random(1)->first()->id,
                    ];
                })
        )->count(30)->create();
        Cliente::factory()->count(8)->create();
        User::factory()->create();
    }
}
