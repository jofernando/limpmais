<?php

namespace App\Models;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\Filter;
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
        'gastos',
        'produto_id',
        'tipo_quantidade',
        'quantidade',
        'folguista',
        'prazo',
        'venda',
        'outros',
        'motorista_id',
        'veiculo_id',
        'fornecedor_id',
    ];

    protected $casts = [
        'vencimento' => 'datetime',
        'pagamento' => 'datetime',
    ];

    /**
     * Get the cliente that owns the Duplicata
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Get the produto that owns the Duplicata
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    private function duplicataFornecedorProduto()
    {
        return Duplicata::where('fornecedor_id', $this->fornecedor_id)->where('produto_id', $this->produto_id);
    }

    public function getValorVendidoAttribute()
    {
        return $this->duplicataFornecedorProduto()->sum('valor');
    }

    public function getSacos50Attribute()
    {
        return $this->duplicataFornecedorProduto()->where('tipo_quantidade', 'sacos50')->sum('quantidade');
    }

    public function getSacos40Attribute()
    {
        return $this->duplicataFornecedorProduto()->where('tipo_quantidade', 'sacos40')->sum('quantidade');
    }

    public function getSacos60Attribute()
    {
        return $this->duplicataFornecedorProduto()->where('tipo_quantidade', 'sacos60')->sum('quantidade');
    }


    public function getToneladasAttribute()
    {
        return $this->duplicataFornecedorProduto()->where('tipo_quantidade', 'toneladas')->sum('quantidade');
    }

    /**
     * Get the veiculo that owns the Duplicata
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Get the motorista that owns the Duplicata
     */
    public function motorista(): BelongsTo
    {
        return $this->belongsTo(Motorista::class);
    }

    /**
     * Get the fornecedor that owns the Duplicata
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function getPagamentoRestanteAttribute(): string
    {
        return number_format($this->valor - $this->pagamentos()->sum('valor'), 2, '.', '');
    }

    public function getPagamentoEfetuadoAttribute(): string
    {
        return number_format($this->pagamentos()->sum('valor'), 2, '.', '');
    }

    public function getStatusAttribute(): string
    {
        $valor_pago = $this->pagamentos()->sum('valor');
        if ($this->valor <= $valor_pago) {
            return 'pago';
        }
        if ($this->vencimento < now()) {
            return 'vencido';
        } else {
            return 'pendente';
        }
    }

    public static function getForm(): array
    {
        return [
            Grid::make()
                ->schema([
                    PtbrMoney::make('valor')
                        ->required(),
                    Radio::make('prazo')
                        ->options([
                            7 => 7,
                            14 => 14,
                            21 => 21,
                            30 => 30,
                        ])
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Closure $set, Closure $get) => $get('venda') ? $set('vencimento', (new Carbon($get('venda')))->addDays($state)) : $get('vencimento')),
                    DatePicker::make('venda')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, Closure $set, Closure $get) => $get('prazo') ? $set('vencimento', (new Carbon($state))->addDays($get('prazo'))) : $get('vencimento')),
                    DatePicker::make('vencimento')
                        ->required()
                        ->default(now()->addDays(7)),
                    PtbrMoney::make('compra')->reactive(),
                    PtbrMoney::make('gastos')->reactive(),
                    Placeholder::make('final')
                        ->content(function ($get) {
                            $gastos = str_replace('.', '', $get('gastos'));
                            $compra = str_replace('.', '', $get('compra'));
                            $gastos = str_replace(',', '.', $gastos);
                            $compra = str_replace(',', '.', $compra);

                            return number_format(floatval($compra) + floatval($gastos), '2', ',', '.');
                        })
                        ->reactive(),
                    Placeholder::make('lucro')
                        ->content(function ($get) {
                            $gastos = str_replace('.', '', $get('gastos'));
                            $gastos = str_replace(',', '.', $gastos);
                            $compra = str_replace('.', '', $get('compra'));
                            $compra = str_replace(',', '.', $compra);
                            $valor = str_replace('.', '', $get('valor'));
                            $valor = str_replace(',', '.', $valor);

                            return number_format(floatval($valor) - floatval($compra) - floatval($gastos), '2', ',', '.');
                        })
                        ->reactive(),
                    Repeater::make('pagamentos')
                        ->schema([
                            PtbrMoney::make('valor')
                                ->reactive(),
                            DatePicker::make('data')->requiredWith('valor'),
                            Select::make('metodo_pagamento_id')
                                ->relationship('metodoPagamento', 'tipo'),
                        ])
                        ->defaultItems(0)
                        ->relationship()
                        ->columns(3)
                        ->columnSpan(2)
                        ->reactive(),
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

                            return 'R$ '.number_format(floatval($valor) - $result, '2', ',', '.');
                        }),
                    PlaceHolder::make('efetuado')
                        ->label('Pagamento efetuado')
                        ->content(function ($get) {
                            $result = collect($get('pagamentos'))->pluck('valor')->map(function ($item) {
                                $valor = str_replace('.', '', $item);
                                $valor = str_replace(',', '.', $valor);

                                return floatval($valor);
                            })->sum();

                            return 'R$ '.number_format($result, '2', ',', '.');
                        }),
                    RichEditor::make('observacao')
                        ->label('Observação')
                        ->columnSpan(2),
                    Select::make('fornecedor_id')
                        ->label('Fornecedor')
                        ->options(Fornecedor::all()->pluck('empresa', 'id')),
                    Select::make('produto_id')
                        ->label('Produto')
                        ->options(Produto::all()->pluck('nome', 'id')),
                    TextInput::make('outros')->label('Outros produtos')->columnSpan(2),
                    Select::make('tipo_quantidade')
                        ->label('Tipo da quantidade')
                        ->options([
                            'toneladas' => 'Toneladas',
                            'sacos40' => 'Sacos 40kg',
                            'sacos50' => 'Sacos 50kg',
                            'sacos60' => 'Sacos 60kg',
                        ]),
                    TextInput::make('quantidade')
                        ->numeric(),
                    Select::make('motorista_id')
                        ->label('Motorista')
                        ->options(Motorista::all()->pluck('nome', 'id')),
                    Select::make('veiculo_id')
                        ->label('Veiculo')
                        ->options(Veiculo::all()->pluck('placa', 'id')),
                    TextInput::make('folguista')
                        ->columnSpan(2),
                ])
                ->columns(2),

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
                        fn (Builder $query) => $query->where(fn ($query) => $query->whereHas('pagamentos', fn ($query) => $query->select(DB::raw('SUM(pagamentos.valor)'))->groupBy('duplicata_id')->havingRaw('SUM(pagamentos.valor) <> duplicatas.valor'))->orWhereDoesntHave('pagamentos'))
                    );
            });
    }

    public static function statusVencimento()
    {
        return Filter::make('vencimento')
            ->form([
                Radio::make('vencimento')
                    ->label('Vencimento')
                    ->options([
                        7 => '7 dias',
                        14 => '14 dias',
                        21 => '21 dias',
                        30 => '30 dias',
                    ]),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query->when($data['vencimento'], fn (Builder $query) => $query->whereBetween('vencimento', [now(), now()->addDays($data['vencimento'])]));
            });
    }
}
