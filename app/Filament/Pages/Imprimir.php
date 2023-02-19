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

    public $customers = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('customers')
                ->schema([
                    Select::make('customer_id')
                        ->required()
                        ->reactive()
                        ->label('Código/nome do customer')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => Customer::where('nome', 'ilike', "%{$search}%")->orWhere('id', intval($search))->limit(10)->pluck('nome', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->nome),
                ])
                ->columns(1)
                ->disableItemMovement(),
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('adicionarMuitosCustomers')
                ->action(function (array $data): void {
                    $exploded = explode(',', $data['codigos']);
                    foreach ($exploded as $item) {
                        $customer = ['customer_id' => $item];
                        $this->customers[] = $customer;
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
