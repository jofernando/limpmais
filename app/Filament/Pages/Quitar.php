<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Duplicata;
use App\Rules\CustomersCadastrados;
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
class Quitar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.quitar';

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
                    TextInput::make('divida')->numeric()->disabled()->label('Dívida'),
                ])
                ->columns(1)
                ->disableItemMovement(),
        ];
    }

    public function submit()
    {
        Duplicata::whereIn('customer_id', array_column($this->duplicatas, 'customer_id'))->where('quitada', false)->update(['quitada' => true]);
        $this->notify('success', 'Duplicatas quitadas com sucesso.');
        return $this->redirectRoute('filament.pages.quitar');
    }
}
