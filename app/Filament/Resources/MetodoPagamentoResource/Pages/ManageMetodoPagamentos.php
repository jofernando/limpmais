<?php

namespace App\Filament\Resources\MetodoPagamentoResource\Pages;

use App\Filament\Resources\MetodoPagamentoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMetodoPagamentos extends ManageRecords
{
    protected static string $resource = MetodoPagamentoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
