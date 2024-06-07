<?php

namespace Database\Seeders;

use App\Imports\ClienteImport;
use App\Imports\DuplicataImport;
use App\Models\Cliente;
use App\Models\Duplicata;
use App\Models\Pagamento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Cliente::factory()->has(
            Duplicata::factory()->count(3)->has(
                Pagamento::factory()->count(2)->state(
                    function (array $attributes, Duplicata $duplicata) {
                        return ['valor' => $duplicata->valor / 3];
                    }
                )
            )
        )->count(30)->create();
        Cliente::factory()->count(8)->create();
        User::factory()->create();
    }
}
