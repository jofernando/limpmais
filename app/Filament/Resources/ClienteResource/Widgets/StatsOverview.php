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
        $mes = [now()->subDays(30), now()];
        $recebidos = Duplicata::whereBetween('pagamento', $mes)->sum('pago');
        $duplicatas = Duplicata::whereBetween('created_at', $mes)->whereNotNull('compra')->get();
        $compras = $duplicatas->sum('compras');
        $compras = $duplicatas->sum('gastos');
        $lucro = $duplicatas->sum('valor') - $compras;
        return [
            Card::make('Clientes cadastrados', Cliente::count()),
            Card::make('Duplicatas recebidas nos ultimos 30 dias', "R$ " . number_format($recebidos, 2, ',', '.')),
            Card::make('Lucro nos ultimos 30 dias', "R$ " . number_format($lucro, 2, ',', '.')),
        ];
    }
}
