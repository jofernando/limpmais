<?php

namespace App\Filament\Resources\FornecedorResource\Pages;

use App\Filament\Resources\FornecedorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFornecedor extends ViewRecord
{
    protected static string $resource = FornecedorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected $listeners = ['atualizarSaldoReceber' => 'render'];
}
