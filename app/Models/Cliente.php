<?php

namespace App\Models;

use Filament\Forms\Components\Radio;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'rua',
        'numero',
        'cidade',
        'estado',
        'ponto_referencia',
        'observacao',
        'setor',
        'cpf_cnpj'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['divida'];

    /**
     * Get all the duplicatas for the Cliente
     *
     * @return HasMany
     */
    public function duplicatas(): HasMany
    {
        return $this->hasMany(Duplicata::class);
    }

    public function getIdentificacaoAttribute(): string
    {
        $rua = $this->rua;
        if ($this->numero != null) {
            $rua = $rua . " Nº " .$this->numero;
        }
        $linksArray = [$this->nome, $this->ponto_referencia, $rua, $this->cidade];
        $linksArray = array_filter($linksArray, fn($value) => !is_null($value) && $value !== '' && $value !== ' ');
        return implode(", ", $linksArray);
    }

    public function getEnderecoAttribute(): string
    {
        $rua = $this->rua;
        if ($this->numero != null) {
            $rua = $rua . " Nº " .$this->numero;
        }
        $linksArray = [$rua, $this->cidade];
        $linksArray = array_filter($linksArray, fn($value) => !is_null($value) && $value !== '' && $value !== ' ');
        return implode(", ", $linksArray);
    }

    public function getDividaAttribute()
    {
        $valor = $this->duplicatas()->sum('valor');
        $pago = $this->duplicatas()->withSum('pagamentos', 'valor')->get()->sum('pagamentos_sum_valor');
        return number_format($valor - $pago, 2, '.', '');
    }

    public static function statusFilter()
    {
        return Filter::make('status')
        ->form([
            Radio::make('option')
                ->label('Status')
                ->options([
                    'inadimplente' => 'Inadimplente',
                    'emdias' => 'Em dias',
                    'semdebito' => 'Sem débito'
                ]),
        ])
        ->query(function (Builder $query, array $data): Builder {
            return $query
                ->when(
                    $data['option'] == 'inadimplente',
                    function (Builder $query): Builder {
                        return $query
                            ->whereHas(
                                'duplicatas', 
                                function ($query) { //clientes que possuem duplicatas
                                    $query->where(function ($query) { //vencidas que não possuem nenhum pagamento
                                        $query
                                            ->where('vencimento', '<', now())
                                            ->doesntHave('pagamentos');
                                    })->orWhereHas(
                                        'pagamentos',
                                        function ($query) { //ou vencidas, que possuem algum pagamento, mas não pagou todo o valor da duplicata
                                            $query->select(DB::raw('SUM(valor) as soma'))
                                                ->where('duplicatas.vencimento', '<', now())
                                                ->groupBy('duplicata_id')
                                                ->havingRaw('SUM(valor) <> duplicatas.valor');
                                        }
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $data['option'] == 'emdias',
                    function ($query) {
                        $query->whereDoesntHave('duplicatas', function ($query) { //clientes que não possuem duplicatas especificas
                            $query->where('duplicatas.vencimento', '<', now()) //duplicatas que o prazo já venceu
                            ->where(function ($query) { //e
                                $query->doesntHave('pagamentos') //duplicata que não possui pagamento
                                    ->orWhereHas('pagamentos', function ($query) { //ou duplicata que possui pagamento, mas não pagou todo o valor da duplicata
                                        $query->select(DB::raw('SUM(valor) as soma'))
                                            ->groupBy('duplicata_id')
                                            ->havingRaw('SUM(valor) <> duplicatas.valor');
                                });
                            });
                        })
                        ->whereHas('duplicatas', function ($query) { //clientes que possuem duplicatas não pagas, mas não vencidas
                            $query->where('vencimento', '>=', now()) //duplicatas não vencidas
                                ->where(function($query) {
                                    $query->doesntHave('pagamentos') //duplicatas sem pagamentos
                                        ->orWhereHas('pagamentos', function ($query) { //duplicatas pagas parcialmente
                                            $query->select(DB::raw('SUM(valor) as soma'))
                                                ->groupBy('duplicata_id')
                                                ->havingRaw('SUM(valor) <> duplicatas.valor');
                                });
                            });
                        });
                    }
                )
                ->when(
                    $data['option'] == 'semdebito',
                    function ($query) {
                        $query->doesntHave('duplicatas') //clientes que não possuem duplicatas
                            ->orWhereDoesntHave('duplicatas', function ($query) { //clientes que não possuem duplicatas pagas parcialmente
                                $query->doesntHave('pagamentos')
                                    ->orWhereHas('pagamentos', function ($query) {
                                        $query->select(DB::raw('SUM(valor) as soma'))
                                            ->groupBy('duplicata_id')
                                            ->havingRaw('SUM(valor) <> duplicatas.valor');
                                    });
                        });
                    }
                );
        });
    }
}
