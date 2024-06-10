<?php

namespace App\Imports;

use App\Models\Duplicata;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class DuplicataImport implements SkipsEmptyRows, SkipsOnFailure, ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable;

    /**
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
        if ($row['valpag']) {
            $pago = $row['valpag'];
        }

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
     * @param  Failure[]  $failures
     */
    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }
}
