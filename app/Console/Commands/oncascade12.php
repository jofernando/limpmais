<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class oncascade12 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:oncascade12';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $chaves = [
            ['duplicatas', 'cliente_id', 'clientes'],
            ['motoristas', 'fornecedor_id', 'fornecedors'],
            ['contratos', 'fornecedor_id', 'fornecedors'],
            ['entregas', 'contrato_id', 'contratos'],
            ['entregas', 'motorista_id', 'motoristas'],
            ['resgates', 'fornecedor_id', 'fornecedors'],
            ['pagamentos', 'duplicata_id', 'duplicatas'],
        ];
        foreach ($chaves as $valor) {
            $tabela = $valor[0];
            $chave = $valor[1];
            $outra = $valor[2];
            if (in_array($tabela, ['entregas', 'resgates'])) {
            } else {
                Schema::table($tabela, function ($table) use ($chave) {
                    $table->dropForeign([$chave]);
                });
            }
            Schema::table($tabela, function ($table) use ($chave, $outra) {
                $table->foreign($chave)->references('id')->on($outra)->cascadeOnDelete();
            });
        }
    }
}
