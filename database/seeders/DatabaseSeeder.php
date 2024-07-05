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
use App\Models\Veiculo;
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
        $fornecedores = Fornecedor::factory()->count(5)->create();
        $motoristas = Motorista::factory()->count(5)->create();
        $veiculos = Veiculo::factory()->count(5)->create();
        $produtos = Produto::factory()->count(3)->create();
        Cliente::factory()->has(
            Duplicata::factory()
                ->count(3)
                ->recycle($motoristas)
                ->recycle($veiculos)
                ->has(
                    Pagamento::factory()->count(2)
                        ->recycle($credcards)
                        ->state(
                            function (array $attributes, Duplicata $duplicata) {
                                return [
                                    'valor' => $duplicata->valor / 3,
                                ];
                            }
                        )
                )
                ->has(
                    Item::factory()->count(2)
                        ->recycle($produtos)
                        ->recycle($fornecedores),
                    'itens'
                )
            )
            ->count(30)->create();
        Cliente::factory()->has(
                Duplicata::factory()
                    ->count(3)
                    ->recycle($motoristas)
                    ->recycle($veiculos)
                    ->has(
                        Pagamento::factory()->recycle($credcards)
                            ->state(
                                function (array $attributes, Duplicata $duplicata) {
                                    return [
                                        'valor' => $duplicata->valor,
                                    ];
                                }
                            )
                    )
                    ->has(
                        Item::factory()->count(2)
                            ->recycle($produtos)
                            ->recycle($fornecedores),
                        'itens'
                    )
                )
                ->count(30)->create();
        Cliente::factory()->has(
                Duplicata::factory()
                    ->count(3)
                    ->vencida()
                    ->recycle($motoristas)
                    ->recycle($veiculos)
                    ->has(
                        Item::factory()->count(2)
                            ->recycle($produtos)
                            ->recycle($fornecedores),
                        'itens'
                    )
                )
                ->count(30)->create();
        Cliente::factory()->count(8)->create();
        User::factory()->create();
    }
}
