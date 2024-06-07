<?php
 
namespace App\Filament\Pages;

use App\Models\Duplicata;
use App\Models\MetodoPagamento;
use App\Models\Pagamento;
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
        $this->inicio = now()->startOfMonth()->addDay();
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
            'valor' => "R$ " . number_format($recebidos, 2, ',', '.'),
        ];

        foreach (MetodoPagamento::all() as $mp) {
            $this->stats[] = [
                'titulo' => $mp->tipo,
                'valor' => "R$ " . number_format(Pagamento::whereBetween('data', $periodo)->where('metodo_pagamento_id', $mp->id)->sum('valor'), 2, ',', '.'),
            ];
        }

        $this->stats[] = [
            'titulo' => 'Compras das duplicatas',
            'valor' => "R$ " . number_format($compras, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Vendas das duplicatas',
            'valor' => "R$ " . number_format($vendas, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Gastos das duplicatas',
            'valor' => "R$ " . number_format($gastos, 2, ',', '.'),
        ];
        $this->stats[] = [
            'titulo' => 'Lucro',
            'valor' => "R$ " . number_format($lucro, 2, ',', '.'),
        ];
    }
}