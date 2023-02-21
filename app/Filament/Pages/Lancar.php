<?php

namespace App\Filament\Pages;

use App\Models\Customer;
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

    public $customers = [
        [
            'customer_id' => '',
            'nome' => '',
            'divida' => '',
            'pago' => '',
            'comprado' => '',
        ],
    ];

    protected function getActions(): array
    {
        return [
            Action::make('adicionarMuitosCustomers')
                ->action(function (array $data): void {
                    $exploded = explode(',', $data['valores']);
                    $chunked = array_chunk($exploded, 3);
                    foreach ($chunked as $item) {
                        $customer = Customer::find($item[0]);
                        if ($customer) {
                            $this->customers[] = [
                                'customer_id' => $item[0],
                                'nome' => $customer->nome,
                                'divida' => $customer->divida,
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

    public function adicionarCustomer()
    {
        $this->customers[] = [
            'customer_id' => '',
            'nome' => '',
            'divida' => '',
            'pago' => '',
            'comprado' => ','
        ];
        $this->dispatchBrowserEvent('focus_next_input', ['index' => array_key_last($this->customers)]);
    }

    public function removerCustomer($index)
    {
        unset($this->customers[$index]);
    }

    public function setarValores($index)
    {
        $customer = Customer::find($this->customers[$index]['customer_id']);
        if ($customer) {
            $this->customers[$index]['divida'] = $customer->divida;
            $this->customers[$index]['nome'] = $customer->identificacao;
            $this->customers[$index]['pago'] = $customer->divida;
        } else {
            $this->customers[$index]['customer_id'] = null;
        }
        $this->dispatchBrowserEvent('select_text_in_input_with_focus');
    }

    public function submit()
    {
        foreach ($this->customers as $item) {
            $customer = Customer::find($item['customer_id']);
            $duplicatas = $customer->duplicatas()->where('quitada', false)->get();
            $valorPago = floatval($item['pago']);
            $valorComprado = floatval($item['comprado']);
            foreach ($duplicatas as $dupl) {
                if ($valorPago >= $dupl->valor) {
                    $dupl->quitada = true;
                    $valorPago -= $dupl->valor;
                    $dupl->save();
                } elseif ($valorPago > 0) {
                    $dupl->quitada = true;
                    $restante = $dupl->valor - $valorPago;
                    $novaDuplicata = Duplicata::create(['valor' => $restante, 'vencimento' => $dupl->vencimento, 'customer_id' => $customer->id]);
                    $dupl->observacao = "Duplicata paga parcialmente. Valor pago: {$valorPago}. Restante {$restante}. Nova duplicata gerada {$novaDuplicata->id}";
                    $valorPago = 0;
                    $dupl->save();
                }
            }
            if($valorComprado > 0) {
                Duplicata::create(['valor' => $valorComprado, 'vencimento' => Carbon::now()->addDays(30), 'customer_id' => $customer->id]);
            }
            $this->notify('success', 'Duplicatas lançadas com sucesso.');
        }
    }

    public function submitAndPrint()
    {
        $this->submit();
        $duplicatas = array_map(fn($item) => $this->mapHelper($item), $this->customers);
        $duplicatas = array_filter($duplicatas, fn($item) => $item['divida'] > 0);
        $data = now()->format('d/m/Y');
        $hora = now()->format('H:i:s');
        $pdf = \PDF::loadView('impressao.duplicatas', compact('duplicatas', 'data', 'hora'))->output();
        return response()->streamDownload( fn () => print($pdf), "duplicatas.pdf");
    }

    private function mapHelper($item)
    {
        $customer = Customer::find($item['customer_id']);
        $item['codigo'] = $customer->id;
        $item['nome'] = $customer->identificacao;
        $item['divida'] = $customer->divida;
        $item['data_vencimento'] = $customer->duplicatas()->where('quitada', false)->first()?->vencimento->format('d/m/Y');
        return $item;
    }
}
