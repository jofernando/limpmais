<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use App\Models\Cliente;
use App\Models\Duplicata;
use App\Models\Pagamento;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $mes = [now()->subDays(30), now()];
        $recebidos = Pagamento::whereBetween('data', $mes)->sum('valor');
        $duplicatas = Duplicata::whereBetween('venda', $mes)->get();
        $vendas = $duplicatas->sum('valor');
        $compras = $duplicatas->sum('compra');
        $gastos = $duplicatas->sum('gastos');
        $lucro = $vendas - $compras - $gastos;
        return [
            Card::make('Clientes cadastrados', Cliente::count()),
            Card::make('Duplicatas recebidas nos ultimos 30 dias', "R$ " . number_format($recebidos, 2, ',', '.')),
            Card::make('Compras das duplicatas nos ultimos 30 dias', 'R$'. number_format($compras, 2, ',', '.')),
            Card::make('Vendas das duplicatas nos ultimos 30 dias', 'R$'. number_format($vendas, 2, ',', '.')),
            Card::make('Gastos das duplicatas nos ultimos 30 dias', 'R$'. number_format($gastos, 2, ',', '.')),
            Card::make('Lucro estimado nos ultimos 30 dias', "R$ " . number_format($lucro, 2, ',', '.')),
        ];
    }
}
