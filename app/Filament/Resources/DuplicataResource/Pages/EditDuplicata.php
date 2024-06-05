<?php

namespace App\Filament\Resources\DuplicataResource\Pages;

use App\Filament\Resources\DuplicataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDuplicata extends EditRecord
{
    protected static string $resource = DuplicataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
