<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Duplicata;
use App\Rules\CustomersCadastrados;
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
class Quitar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.quitar';
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
                Wizard\Step::make('Quitar dívidas')
                    ->schema([
                        Textarea::make('texto')
                            ->label('Customers')
                            ->rows(10)
                            ->required()
                            ->rule(new CustomersCadastrados())
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
            $validator = Validator::make(['texto' => $this->texto], ['texto' => ['required', new CustomersCadastrados()]]);
            if (!$validator->fails()) {
                $this->duplicatas = [];
                $exploded = explode(',', $this->texto);
                $customers = Customer::whereIn('id', $exploded)->get();
                $this->duplicatas = $customers->map(fn ($customer) => ['codigo' => $customer->id, 'nome' => $customer->identificacao, 'divida' => $customer->divida])->all();
            }
        }
    }

    public function submit()
    {
        Duplicata::whereIn('customer_id', array_column($this->duplicatas, 'codigo'))->where('quitada', false)->update(['quitada' => true]);
        $this->notify('success', 'Duplicatas quitadas com sucesso.');
        return $this->redirectRoute('filament.pages.quitar');
    }
}
