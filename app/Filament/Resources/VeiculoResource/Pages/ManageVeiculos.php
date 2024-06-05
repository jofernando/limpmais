<?php

namespace App\Filament\Resources\VeiculoResource\Pages;

use App\Filament\Resources\VeiculoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageVeiculos extends ManageRecords
{
    protected static string $resource = VeiculoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
