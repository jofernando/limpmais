<?php

namespace App\Filament\Resources\PagamentoResource\Pages;

use App\Filament\Resources\PagamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPagamentos extends ListRecords
{
    protected static string $resource = PagamentoResource::class;

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->join('duplicatas', 'pagamentos.duplicata_id', '=', 'duplicatas.id')
            ->join('clientes', 'duplicatas.cliente_id', '=', 'clientes.id')
            ->select('pagamentos.*');
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'duplicata.cliente.nome';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }
}
