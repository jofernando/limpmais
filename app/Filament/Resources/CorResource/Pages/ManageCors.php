<?php

namespace App\Filament\Resources\CorResource\Pages;

use App\Filament\Resources\CorResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCors extends ManageRecords
{
    protected static string $resource = CorResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
