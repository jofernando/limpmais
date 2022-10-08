<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Duplicata;
use App\Rules\Lancamento;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
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

    public $duplicatas = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Wizard\Step::make('Lançamentos')
                    ->schema([
                        Textarea::make('texto')
                            ->label('Duplicatas')
                            ->rows(10)
                            ->required()
                            ->rule(new Lancamento())
                            ->afterStateUpdated(fn() => $this->listarDuplicatas())
                    ]),
                Wizard\Step::make('Confirmação')
                    ->schema([
                        Repeater::make('duplicatas')
                            ->schema([
                                TextInput::make('nome')->disabled()->label('Cliente'),
                                Grid::make()
                                ->schema([
                                    TextInput::make('codigo')->disabled()->label('Código'),
                                    TextInput::make('pagar')->disabled()->label('Valor pago'),
                                    TextInput::make('receber')->disabled()->label('Valor comprado'),
                                    TextInput::make('restante')->disabled()->label('Dívida'),
                                ])
                                ->columns(4)

                            ])
                            ->columns(1)
                            ->disableItemCreation()
                            ->disableItemDeletion()
                            ->disableItemMovement()
                    ]),
            ])
            ->submitAction(Action::make('submit')->button()->submit('form')->label('Enviar'))
        ];
    }

    public function listarDuplicatas()
    {
        if($this->texto) {
            $validator = Validator::make(['texto' => $this->texto], ['texto' => ['required', new Lancamento()]]);
            if (!$validator->fails()) {
                $this->duplicatas = [];
                $exploded = explode(',', $this->texto);
                $chunked = array_chunk($exploded, 3);
                foreach ($chunked as $item) {
                    $customer = Customer::find($item[0]);
                    $divida = $customer->divida;
                    if ($item[1] > 0) {
                        $duplicata = ['codigo' => $item[0], 'nome' => $customer->identificacao, 'pagar' => $item[1], 'receber' => $item[2], 'restante' => $divida - $item[1] + $item[2]];
                    } elseif ($item[1] < 0) {
                        $duplicata = ['codigo' => $item[0], 'nome' => $customer->identificacao, 'pagar' => 0, 'receber' => $item[2], 'restante' => $divida + $item[2]];
                    } else {
                        $duplicata = ['codigo' => $item[0], 'nome' => $customer->identificacao, 'pagar' => $divida, 'receber' => $item[2], 'restante' => $item[2]];
                    }
                    $this->duplicatas[] = $duplicata;
                }
            }
        }
    }

    public function submit()
    {
        foreach ($this->duplicatas as $item) {
            $customer = Customer::find($item['codigo']);
            $duplicatas = $customer->duplicatas()->where('quitada', false)->get();
            $valorPago = floatval($item['pagar']);
            $valorComprado = floatval($item['receber']);
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
}
