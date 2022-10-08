<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Rules\Impressao;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Validator;

class Imprimir extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.imprimir';

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
                            ->rule(new Impressao())
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
                                        TextInput::make('divida')->disabled()->label('Dívida'),
                                    ])
                                    ->columns(2)

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
            $validator = Validator::make(['texto' => $this->texto], ['texto' => ['required', new Impressao()]]);
            if (!$validator->fails()) {
                $this->duplicatas = [];
                $exploded = explode(',', $this->texto);
                $chunked = array_chunk($exploded, 3);
                foreach ($chunked as $item) {
                    $customer = Customer::find($item[0]);
                    $divida = $customer->divida;
                    if ($divida != 0) {
                        $vencimento = $customer->duplicatas()->where('quitada', false)->first()->vencimento->format('d/m/Y');
                        $duplicata = ['codigo' => $item[0], 'nome' => $customer->identificacao, 'divida' => $divida, 'data_vencimento' => $vencimento];
                        $this->duplicatas[] = $duplicata;
                    }
                }
            }
        }
    }

    public function submit()
    {
        $data = now()->format('d/m/Y');
        $hora = now()->format('H:i:s');
        $duplicatas = $this->duplicatas;
        $pdf = \PDF::loadView('impressao.duplicatas', compact('duplicatas', 'data', 'hora'))->output();
        return response()->streamDownload( fn () => print($pdf), "duplicatas.pdf");
    }
}
