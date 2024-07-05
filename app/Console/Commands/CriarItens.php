<?php

namespace App\Console\Commands;

use App\Models\Duplicata;
use App\Models\Item;
use Illuminate\Console\Command;

class CriarItens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:criar-itens';

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
        $duplicatas = Duplicata::all();

        foreach ($duplicatas as $duplicata) {
           Item::create([
                'tipo_quantidade' => $duplicata->tipo_quantidade,
                'quantidade' => $duplicata->quantidade,
                'produto_id' => $duplicata->produto_id,
                'duplicata_id' => $duplicata->id,
                'fornecedor_id' => $duplicata->fornecedor_id,
           ]);
        }
    }
}
