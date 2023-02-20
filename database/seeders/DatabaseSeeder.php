<?php

namespace Database\Seeders;

use App\Imports\CustomerImport;
use App\Imports\DuplicataImport;
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
        Excel::import(new CustomerImport, 'CLIENTES.xls');
        Excel::import(new DuplicataImport, 'DUPLRECE.xls');
    }
}
