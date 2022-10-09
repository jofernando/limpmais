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
                        ->getOptionLabelUsing(fn ($value): ?string => Customer::find($value)?->nome)
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('divida', Customer::find($state)?->divida);
                        }),
                    Grid::make()
                    ->schema([
                        TextInput::make('pago')->numeric()->minValue(0)->maxValue(fn (Closure $get) => $this->recuperarDivida($get('customer_id')))->label('Valor pago')->required(),
                        TextInput::make('comprado')->numeric()->minValue(0)->label('Valor comprado')->required(),
                        TextInput::make('divida')->numeric()->disabled()->label('Dívida'),
                    ])
                    ->columns(3)

                ])
                ->columns(1)
                ->disableItemMovement(),
        ];
    }

    private function recuperarDivida($customer_id)
    {
        if ($customer_id) {
            return Customer::find($customer_id)?->divida;
        }
        return 0;
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
            if($valorComprado > 0)
                Duplicata::create(['valor' => $valorComprado, 'vencimento' => Carbon::now()->addDays(30), 'customer_id' => $customer->id]);
        }
        $this->notify('success', 'Duplicatas lançadas com sucesso.');
        return $this->redirectRoute('filament.pages.lancar');
    }

    public function submitAndPrint()
    {
        $this->submit();
        $duplicatas = array_map(fn($item) => $this->mapHelper($item), $this->customers);
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
        $item['data_vencimento'] = $customer->duplicatas()->where('quitada', false)->first()?->vencimento->format('d/m/Y');
        return $item;
    }
}
