<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class CustomerImport implements ToModel, WithChunkReading, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Customer([
            'nome' => $row['nomcli'],
            'rua' => $row['rua'],
            'cidade' => $row['cidade'],
            'estado' => $row['estado'],
            'ponto_referencia' => $row['pontoref'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
