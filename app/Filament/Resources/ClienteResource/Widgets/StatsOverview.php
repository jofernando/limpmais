<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use App\Models\Duplicata;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Clientes cadastrados', Cliente::count()),
            Card::make('Duplicatas recebidas no último ano', "R$ " . number_format(Duplicata::whereBetween('pagamento', [now()->subYear(), now()])->sum('pago'), 2, ',', '.')),
        ];
    }
}
