<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Duplicata;
use App\Rules\Lancamento;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Validator;

class Lancar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.lancar';

    protected static ?string $navigationLabel = 'Lançar';

    protected static ?string $title = 'Lançar';

    public $texto;

    public $clientes = [
        [
            'cliente_id' => '',
            'nome' => '',
            'divida' => '',
            'pago' => '',
            'comprado' => '',
        ],
    ];

    protected function getActions(): array
    {
        return [
            Action::make('adicionarMuitosClientes')
                ->action(function (array $data): void {
                    $exploded = explode(',', $data['valores']);
                    $chunked = array_chunk($exploded, 3);
                    foreach ($chunked as $item) {
                        $cliente = Cliente::find($item[0]);
                        if ($cliente) {
                            $this->clientes[] = [
                                'cliente_id' => $item[0],
                                'nome' => $cliente->nome,
                                'divida' => $cliente->divida,
                                'pago' => $item[1],
                                'comprado' => $item[2],
                            ];
                        }
                    }
                })
                ->form([
                    Textarea::make('valores')
                        ->label('Valores')
                        ->rows(10)
                        ->rule(new Lancamento())
                        ->required(),
                ])
                ->modalSubheading('Valores no seguinte formato: código do cliente, valor pago, valor comprado (sem espaços)'),
        ];
    }

    public function adicionarCliente()
    {
        $this->clientes[] = [
            'cliente_id' => '',
            'nome' => '',
            'divida' => '',
            'pago' => '',
            'comprado' => ','
        ];
        $this->dispatchBrowserEvent('focus_next_input', ['index' => array_key_last($this->clientes)]);
    }

    public function removerCliente($index)
    {
        unset($this->clientes[$index]);
    }

    public function setarValores($index)
    {
        $cliente = Cliente::find($this->clientes[$index]['cliente_id']);
        if ($cliente) {
            $this->clientes[$index]['divida'] = $cliente->divida;
            $this->clientes[$index]['nome'] = $cliente->identificacao;
            $this->clientes[$index]['pago'] = $cliente->divida;
        } else {
            $this->clientes[$index]['cliente_id'] = null;
        }
        $this->dispatchBrowserEvent('select_text_in_input_with_focus');
    }

    public function submit()
    {
        foreach ($this->clientes as $item) {
            $cliente = Cliente::find($item['cliente_id']);
            $duplicatas = $cliente->duplicatas()->whereNull('pagamento')->get();
            $valorPago = floatval($item['pago']);
            $valorComprado = floatval($item['comprado']);
            foreach ($duplicatas as $dupl) {
                if ($valorPago >= $dupl->valor) {
                    $dupl->pagamento = Carbon::now();
                    $dupl->pago = $dupl->valor;
                    $valorPago -= $dupl->valor;
                    $dupl->save();
                } elseif ($valorPago > 0) {
                    $dupl->pagamento = Carbon::now();
                    $dupl->pago = $valorPago;
                    $restante = $dupl->valor - $valorPago;
                    $novaDuplicata = Duplicata::create(['valor' => $restante, 'vencimento' => $dupl->vencimento, 'cliente_id' => $cliente->id]);
                    $dupl->observacao = "Duplicata paga parcialmente. Valor pago: {$valorPago}. Restante {$restante}. Nova duplicata gerada {$novaDuplicata->id}";
                    $valorPago = 0;
                    $dupl->save();
                }
            }
            if($valorComprado > 0) {
                Duplicata::create(['valor' => $valorComprado, 'vencimento' => Carbon::now()->addDays(30), 'cliente_id' => $cliente->id]);
            }
            $this->notify('success', 'Duplicatas lançadas com sucesso.');
        }
    }

    public function submitAndPrint()
    {
        $this->submit();
        $duplicatas = array_map(fn($item) => $this->mapHelper($item), $this->clientes);
        $duplicatas = array_filter($duplicatas, fn($item) => $item['divida'] > 0);
        $data = now()->format('d/m/Y');
        $hora = now()->format('H:i:s');
        $pdf = \PDF::loadView('impressao.duplicatas', compact('duplicatas', 'data', 'hora'))->output();
        return response()->streamDownload( fn () => print($pdf), "duplicatas.pdf");
    }

    private function mapHelper($item)
    {
        $cliente = Cliente::find($item['cliente_id']);
        $item['codigo'] = $cliente->id;
        $item['nome'] = $cliente->identificacao;
        $item['divida'] = $cliente->divida;
        $item['data_vencimento'] = $cliente->duplicatas()->whereNull('pagamento')->first()?->vencimento->format('d/m/Y');
        return $item;
    }
}
