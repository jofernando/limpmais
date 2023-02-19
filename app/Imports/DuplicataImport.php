<?php

namespace App\Imports;

use App\Models\Duplicata;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DuplicataImport implements ToModel, WithChunkReading, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure, WithBatchInserts
{
    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Duplicata([
            'valor' => $row['totdeb'],
            'vencimento' => now()->addDays(30),
            'customer_id' => $row['codcli'],
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
            'totdeb' => ['required', 'numeric', 'min:1']
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }

}
