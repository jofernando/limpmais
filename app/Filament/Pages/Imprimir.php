<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
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

    public static function canAccess(): bool
    {
        return false;
    }

    public $clientes = [
        [
            'cliente_id' => '',
            'nome' => '',
            'divida' => '',
        ],
    ];

    public function adicionarCliente()
    {
        $this->clientes[] = [
            'cliente_id' => '',
            'nome' => '',
            'divida' => '',
        ];
        $this->dispatch('focus_next_input', ['index' => array_key_last($this->clientes)]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('adicionarMuitosClientes')
                ->action(function (array $data): void {
                    $exploded = explode(',', $data['codigos']);
                    foreach ($exploded as $item) {
                        $cliente = Cliente::find($item);
                        if ($cliente) {
                            $this->clientes[] = [
                                'cliente_id' => $item,
                                'nome' => $cliente->nome,
                                'divida' => $cliente->divida,
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

    public function removerCliente($index)
    {
        unset($this->clientes[$index]);
    }

    public function setarValores($index)
    {
        $cliente = Cliente::find($this->clientes[$index]['cliente_id']);
        if ($cliente) {
            $this->clientes[$index]['divida'] = $cliente->divida;
            $this->clientes[$index]['nome'] = $cliente->nome;
        } else {
            $this->clientes[$index]['cliente_id'] = null;
        }
        $this->dispatch('select_text_in_input_with_focus');
    }

    public function submit()
    {
        $duplicatas = array_map(fn($item) => $this->mapHelper($item), $this->clientes);
        $data = now()->format('d/m/Y');
        $hora = now()->format('H:i:s');
        $pdf = PDF::loadView('impressao.duplicatas', compact('duplicatas', 'data', 'hora'))->output();
        return response()->streamDownload( fn () => print($pdf), "duplicatas.pdf");
    }

    private function mapHelper($item)
    {
        $cliente = Cliente::find($item['cliente_id']);
        $item['codigo'] = $cliente->id;
        $item['divida'] = $cliente->divida;
        $item['nome'] = $cliente->nome;
        $item['data_vencimento'] = $cliente->duplicatas()->whereNull('pagamento')->first()?->vencimento->format('d/m/Y');
        return $item;
    }
}
