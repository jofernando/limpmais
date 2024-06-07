<?php

namespace App\Filament\Resources\TamanhoResource\Pages;

use App\Filament\Resources\TamanhoResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTamanhos extends ManageRecords
{
    protected static string $resource = TamanhoResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
