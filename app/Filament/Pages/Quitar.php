<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use App\Models\Duplicata;
use App\Rules\ClientesCadastrados;
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

    public $clientes = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('clientes')
                ->schema([
                    Select::make('cliente_id')
                        ->required()
                        ->reactive()
                        ->label('Código/nome do cliente')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => Cliente::where('nome', 'ilike', "%{$search}%")->orWhere('id', intval($search))->limit(10)->pluck('nome', 'id'))
                        ->getOptionLabelUsing(fn ($value): ?string => Cliente::find($value)?->nome)
                        ->afterStateUpdated(function (Closure $set, $state) {
                            $set('divida', Cliente::find($state)?->divida);
                        }),
                    TextInput::make('divida')->numeric()->disabled()->label('Dívida'),
                ])
                ->columns(1)
                ->disableItemMovement(),
        ];
    }

    public function submit()
    {
        Duplicata::whereIn('cliente_id', array_column($this->duplicatas, 'cliente_id'))->where('quitada', false)->update(['quitada' => true]);
        $this->notify('success', 'Duplicatas quitadas com sucesso.');
        return $this->redirectRoute('filament.pages.quitar');
    }
}
