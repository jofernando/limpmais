<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Pages\Actions\Action;
use PDF;

class Imprimir extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.imprimir';

    public $customers = [
        [
            'customer_id' => '',
            'nome' => '',
            'divida' => '',
        ],
    ];

    public function adicionarCustomer()
    {
        $this->customers[] = [
            'customer_id' => '',
            'nome' => '',
            'divida' => '',
        ];
        $this->dispatchBrowserEvent('focus_next_input', ['index' => array_key_last($this->customers)]);
    }

    protected function getActions(): array
    {
        return [
            Action::make('adicionarMuitosCustomers')
                ->action(function (array $data): void {
                    $exploded = explode(',', $data['codigos']);
                    foreach ($exploded as $item) {
                        $customer = Customer::find($item);
                        if ($customer) {
                            $this->customers[] = [
                                'customer_id' => $item,
                                'nome' => $customer->nome,
                                'divida' => $customer->divida,
                            ];
                        }
                    }
                })
                ->form([
                    Textarea::make('codigos')
                        ->label('Códigos')
                        ->rows(10)
                        ->required(),
                ])
                ->modalSubheading('Códigos separados por vírgula. Exemplo: 15487,15487,15487,15487'),
        ];
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
        } else {
            $this->customers[$index]['customer_id'] = null;
        }
        $this->dispatchBrowserEvent('select_text_in_input_with_focus');
    }

    public function submit()
    {
        $duplicatas = array_map(fn($item) => $this->mapHelper($item), $this->customers);
        $data = now()->format('d/m/Y');
        $hora = now()->format('H:i:s');
        $pdf = PDF::loadView('impressao.duplicatas', compact('duplicatas', 'data', 'hora'))->output();
        return response()->streamDownload( fn () => print($pdf), "duplicatas.pdf");
    }

    private function mapHelper($item)
    {
        $customer = Customer::find($item['customer_id']);
        $item['codigo'] = $customer->id;
        $item['divida'] = $customer->divida;
        $item['nome'] = $customer->identificacao;
        $item['data_vencimento'] = $customer->duplicatas()->where('quitada', false)->first()?->vencimento->format('d/m/Y');
        return $item;
    }
}
