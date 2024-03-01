<?php

namespace App\Models;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return number_format($this->valor - $this->pagamentos()->sum('valor'), 2);
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
                ])->columns(3),
            Grid::make()
                ->schema([
                    Repeater::make('pagamentos')
                        ->schema([
                            PtbrMoney::make('valor'),
                            DatePicker::make('data')->requiredWith('valor')
                        ])
                        ->defaultItems(0)
                        ->relationship()
                        ->columns(2)
                ])->columns(1),
            Grid::make()
                ->schema([
                    MarkdownEditor::make('observacao')
                        ->label('Observação'),
                ])->columns(1),
        ];
    }
}
