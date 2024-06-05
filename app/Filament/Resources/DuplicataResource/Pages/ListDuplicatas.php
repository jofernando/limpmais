<?php

namespace App\Filament\Resources\DuplicataResource\Pages;

use App\Filament\Resources\DuplicataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDuplicatas extends ListRecords
{
    protected static string $resource = DuplicataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
