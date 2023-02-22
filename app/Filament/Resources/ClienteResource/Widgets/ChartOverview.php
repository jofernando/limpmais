<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use App\Models\Duplicata;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class ChartOverview extends LineChartWidget
{
    protected static ?string $heading = 'Duplicatas recebidas nos últimos meses ano';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $nomes = [];
        $valores = [];
        for ($i=12; $i >= 0; $i--) { 
            $start = Carbon::now()->startOfMonth()->subMonths($i);
            $end = Carbon::now()->endOfMonth()->subMonths($i);
            $nomes[] = $end->shortMonthName;
            $valores[] = Duplicata::whereBetween('pagamento', [$start, $end])->sum('pago');
        }
        return [
            'datasets' => [
                [
                    'label' => 'Duplicatas recebidas nos últimos meses ano',
                    'data' => $valores,
                ],
            ],
            'labels' => $nomes,
        ];
    }
}
