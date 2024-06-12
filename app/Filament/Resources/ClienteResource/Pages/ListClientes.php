<?php

namespace App\Filament\Resources\ClienteResource\Pages;

use App\Filament\Resources\ClienteResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\View\View;

class ListClientes extends ListRecords
{
    protected static string $resource = ClienteResource::class;

    public array $data_list = [
        'calc_columns' => [
            'divida',
        ],
    ];

    protected function getTableContentFooter(): ?View
    {
        return view('table.footer', $this->data_list);
    }
}
