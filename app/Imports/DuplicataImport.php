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
        $timestamp = ($row['datven'] - 25569) * 86400;
        $vencimento = Carbon::createFromTimestamp($timestamp);
        $pagamento = null;
        if ($row['datpag']) {
            $timestamp = ($row['datpag'] - 25569) * 86400;
            $pagamento = Carbon::createFromTimestamp($timestamp);
        }
        $pago = null;
        if ($row['valpag'])
            $pago = $row['valpag'];
        return new Duplicata([
            'cliente_id' => $row['codcli'],
            'valor' => $row['valdup'],
            'pago' => $pago,
            'vencimento' => $vencimento,
            'pagamento' => $pagamento,
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
