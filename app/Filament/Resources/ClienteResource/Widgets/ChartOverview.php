<?php

namespace App\Filament\Resources\ClienteResource\Widgets;

use App\Models\Duplicata;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class ChartOverview extends LineChartWidget
{
    protected static ?string $heading = 'Duplicatas recebidas nos últimos 30 dias';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $nomes = [];
        $valores = [];
        for ($i=30; $i >= 0; $i--) {
            $start = Carbon::now()->subDays($i);
            $end = Carbon::now()->subDays($i);
            $nomes[] = $end->date;
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
