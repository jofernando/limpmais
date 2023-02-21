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
use Illuminate\Support\Carbon;

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
        $quitada = $row['datpag'] != '';
        $timestamp = ($row['datven'] - 25569) * 86400;
        $vencimento = Carbon::createFromTimestamp($timestamp);

        return new Duplicata([
            'valor' => $row['valdup'],
            'vencimento' => $vencimento,
            'cliente_id' => $row['codcli'],
            'quitada' => $quitada,
        ]);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        return [
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
