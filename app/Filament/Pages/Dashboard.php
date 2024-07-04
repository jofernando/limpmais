<?php

namespace App\Filament\Pages;

use App\Models\Duplicata;
use App\Models\MetodoPagamento;
use App\Models\Pagamento;
use App\Models\Cor;
use App\Models\Tamanho;
use App\Models\Item;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage implements HasForms
{
    use InteractsWithForms;

    public $inicio;

    public $fim;

    public $stats;

    protected static string $view = 'filament.pages.painel';

    public function mount(): void
    {
        $this->inicio = now()->startOfMonth();
        $this->fim = now()->endOfMonth();
        $this->submit();
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    DatePicker::make('inicio'),
                    DatePicker::make('fim'),
                ])
                ->columns(2),
        ];
    }

    public function submit()
    {
        $periodo = [new Carbon($this->inicio), new Carbon($this->fim)];
        $recebidos = Pagamento::whereBetween('data', $periodo)->sum('valor');
        $duplicatas = Duplicata::whereBetween('venda', $periodo)->get();
        $vendas = $duplicatas->sum('valor');
        $compras = $duplicatas->sum('compra');
        $gastos = $duplicatas->sum('gastos');
        $lucro = $vendas - $compras - $gastos;

        $this->stats = [];

        $this->stats[] = [
            'titulo' => 'Duplicatas recebidas',
            'valor' => 'R$ '.number_format($recebidos, 2, ',', '.'),
        ];

        foreach (MetodoPagamento::all() as $mp) {
            if (! $mp->pagamento_futuro) {
                $this->stats[] = [
                    'titulo' => $mp->tipo,
                    'valor' => 'R$ '.number_format(Pagamento::whereBetween('data', $periodo)->where('metodo_pagamento_id', $mp->id)->sum('valor'), 2, ',', '.'),
                ];
            } else {
                $this->stats[] = [
                    'titulo' => $mp->tipo,
                    'valor' => 'R$ '.number_format(Pagamento::whereBetween('data', $periodo)->where('metodo_pagamento_id', $mp->id)->where(fn($query) => $query->where('pagamento_futuro', '<=', now())->orWhereNull('pagamento_futuro'))->sum('valor'), 2, ',', '.'),
                ];
                $this->stats[] = [
                    'titulo' => $mp->tipo.' não compensado',
                    'valor' => 'R$ '.number_format(Pagamento::whereBetween('data', $periodo)->where('metodo_pagamento_id', $mp->id)->where('pagamento_futuro', '>', now())->sum('valor'), 2, ',', '.'),
                ];
            }
        }

        $this->stats[] = [
            'titulo' => 'Compras das duplicatas',
            'valor' => 'R$ '.number_format($compras, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Vendas das duplicatas',
            'valor' => 'R$ '.number_format($vendas, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Gastos das duplicatas',
            'valor' => 'R$ '.number_format($gastos, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Lucro',
            'valor' => 'R$ '.number_format($lucro, 2, ',', '.'),
        ];

        $prazos = [7,14,21,30];
        foreach ($prazos as $prazo) {
            $valor = Duplicata::whereBetween('vencimento', [now(), now()->addDays($prazo)])->sum('valor');
            $pago = Duplicata::whereBetween('vencimento', [now(), now()->addDays($prazo)])->withSum('pagamentos', 'valor')->get()->sum('pagamentos_sum_valor');
            $this->stats[] = [
                'titulo' => 'Duplicatas a receber em até '.($prazo).' dias',
                'valor' => 'R$ '.number_format($valor - $pago, 2, ',', '.'),
            ];
        }

        $cores = Cor::all();
        $tamanhos = Tamanho::all();
        foreach ($cores as $cor) {
            foreach ($tamanhos as $tamanho) {
                $qtd = Item::whereIn('duplicata_id', $duplicatas->pluck('id'))
                    ->where([['cor_id', $cor->id], ['tamanho_id', $tamanho->id]])
                    ->get()->sum('quantidade');
                $this->stats[] = [
                    'titulo' => $cor->cor .' - '. $tamanho->tamanho,
                    'valor' => $qtd,
                ];
            }
        }
    }
}
