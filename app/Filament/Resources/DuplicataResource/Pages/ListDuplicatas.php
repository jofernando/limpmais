<?php

namespace App\Filament\Resources\DuplicataResource\Pages;

use App\Filament\Resources\DuplicataResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\View\View;

class ListDuplicatas extends ListRecords
{
    protected static string $resource = DuplicataResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public array $data_list = [
        'calc_columns' => [
            'pagamento_restante',
        ],
    ];

    protected function getTableContentFooter(): ?View
    {
        return view('table.footer', $this->data_list);
    }
}
