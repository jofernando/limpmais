<?php

namespace App\Jobs;

use App\Imports\ClienteImport;
use App\Imports\DuplicataImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportarClientes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Excel::import(new ClienteImport, storage_path("app/CLIENTES.xls"));
        Excel::import(new DuplicataImport, storage_path('app/CLIENTES.xls'));
    }
}
