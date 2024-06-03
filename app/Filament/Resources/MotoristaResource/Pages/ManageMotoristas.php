<?php

namespace App\Filament\Resources\MotoristaResource\Pages;

use App\Filament\Resources\MotoristaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMotoristas extends ManageRecords
{
    protected static string $resource = MotoristaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
