<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->duplicatas()->whereNull('pagamento')->sum('valor');
    }
}
