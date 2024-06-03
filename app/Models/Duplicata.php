<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentPtbrFormFields\PtbrMoney;

class Duplicata extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'pago',
        'vencimento',
        'pagamento',
        'observacao',
        'cliente_id',
        'compra',
        'gastos'
    ];

    protected $casts = [
        'vencimento' => 'datetime',
        'pagamento' => 'datetime',
    ];

    /**
     * Get the cliente that owns the Duplicata
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    public function getPagamentoRestanteAttribute(): string
    {
        return number_format($this->valor - $this->pagamentos()->sum('valor'), 2, '.', '');
    }

    public function getStatusAttribute(): string
    {
        $valor_pago = $this->pagamentos()->sum('valor');
        if($this->valor <= $valor_pago) return 'pago';
        if($this->vencimento < now()) return 'vencido';
        else return 'pendente';
    }

    public static function getForm(): array
    {
        return [
            PtbrMoney::make('valor')
                ->required(),
            DatePicker::make('vencimento')
                ->required()
                ->default(now()->addDays(30)),
            Grid::make()
                ->schema([
                    PtbrMoney::make('compra')->reactive(),
                    PtbrMoney::make('gastos')->reactive(),
                    Placeholder::make('final')
                        ->content(function($get) {
                            $gastos = str_replace('.', '', $get('gastos'));
                            $compra = str_replace('.', '', $get('compra'));
                            $gastos = str_replace(',', '.', $gastos);
                            $compra = str_replace(',', '.', $compra);
                            return number_format(floatval($compra) + floatval($gastos), '2', ',', '.');
                        })
                        ->reactive(),
                    Placeholder::make('lucro')
                        ->content(function($get) {
                            $gastos = str_replace('.', '', $get('gastos'));
                            $gastos = str_replace(',', '.', $gastos);
                            $compra = str_replace('.', '', $get('compra'));
                            $compra = str_replace(',', '.', $compra);
                            $valor = str_replace('.', '', $get('valor'));
                            $valor = str_replace(',', '.', $valor);
                            return number_format(floatval($valor) - floatval($compra) - floatval($gastos), '2', ',', '.');
                        })
                        ->reactive()
                ])->columns(2),
            Grid::make()
                ->schema([
                    Repeater::make('pagamentos')
                        ->schema([
                            PtbrMoney::make('valor')
                                ->reactive(),
                            DatePicker::make('data')->requiredWith('valor'),
                            Select::make('metodo_pagamento_id')
                                ->label('Método de pagamento')
                                ->relationship('metodoPagamento', 'tipo'),
                        ])
                        ->defaultItems(0)
                        ->relationship()
                        ->columns(3)
                        ->reactive()
                ])->columns(1),
            PlaceHolder::make('a_receber')
                ->label('Pagamento restante')
                ->content(function ($get) {
                    $result = collect($get('pagamentos'))->pluck('valor')->map(function ($item) {
                        $valor = str_replace('.', '', $item);
                        $valor = str_replace(',', '.', $valor);
                        return floatval($valor);
                    })->sum();
                    $valor = str_replace('.', '', $get('valor'));
                    $valor = str_replace(',', '.', $valor);
                    return "R$ " . number_format(floatval($valor) - $result, '2', ',', '.');
            }),
            Grid::make()
                ->schema([
                    MarkdownEditor::make('observacao')
                        ->label('Observação'),
                ])->columns(1),
        ];
    }

    public static function statusFilter()
    {
        return Filter::make('status')
            ->form([
                Radio::make('option')
                    ->label('Status')
                    ->options([
                        'liquidadas' => 'Líquidadas',
                        'areceber' => 'A receber',
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['option'] == 'liquidadas',
                        fn (Builder $query) => $query->whereHas('pagamentos', fn ($query) => $query->select(DB::raw('SUM(pagamentos.valor)'))->groupBy('duplicata_id')->havingRaw('SUM(pagamentos.valor) = duplicatas.valor'))
                    )
                    ->when(
                        $data['option'] == 'areceber',
                        fn (Builder $query) => $query->whereHas('pagamentos', fn ($query) => $query->select(DB::raw('SUM(pagamentos.valor)'))->groupBy('duplicata_id')->havingRaw('SUM(pagamentos.valor) <> duplicatas.valor'))->orWhereDoesntHave('pagamentos')
                    );
            });
    }
}
