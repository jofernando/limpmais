<?php

namespace App\Filament\Resources\DuplicataResource\Pages;

use App\Filament\Resources\DuplicataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuplicata extends EditRecord
{
    protected static string $resource = DuplicataResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
