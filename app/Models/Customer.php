<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['divida'];

    /**
     * Get all the duplicatas for the Customer
     *
     * @return HasMany
     */
    public function duplicatas(): HasMany
    {
        return $this->hasMany(Duplicata::class)->orderBy('quitada', 'ASC');
    }

    public function getIdentificacaoAttribute(): string
    {
        $rua = $this->rua;
        if ($this->numero != null) {
            $rua = $rua . " Nº " .$this->numero;
        }
        $linksArray = [$this->nome, $rua, $this->cidade];
        array_filter($linksArray, fn($value) => !is_null($value) && $value !== '' && $value !== ' ');
        return implode(", ", $linksArray);
    }

    public function getEnderecoAttribute(): string
    {
        $rua = $this->rua;
        if ($this->numero != null) {
            $rua = $rua . " Nº " .$this->numero;
        }
        $linksArray = [$rua, $this->cidade];
        array_filter($linksArray, fn($value) => !is_null($value) && $value !== '' && $value !== ' ');
        return implode(", ", $linksArray);
    }

    public function getDividaAttribute()
    {
        return $this->duplicatas()->where('quitada', false)->sum('valor');
    }
}
