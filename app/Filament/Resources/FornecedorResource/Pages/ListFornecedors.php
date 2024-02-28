<?php

namespace App\Filament\Resources\FornecedorResource\Pages;

use App\Filament\Resources\FornecedorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFornecedors extends ListRecords
{
    protected static string $resource = FornecedorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
