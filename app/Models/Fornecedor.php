<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa',
        'cpf_cnpj',
    ];

    protected $appends = ['saldo_receber'];

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    /**
     * Get all of the duplicatas for the Fornecedor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itens(): HasMany
    {
        return $this->hasMany(Item::class)->with('produto')->with('duplicata');
    }

    public function resgates(): HasMany
    {
        return $this->hasMany(Resgate::class);
    }

    public function getSaldoReceberAttribute()
    {
        $saldo = $this->contratos->map(fn ($value) => $value->saldo_receber)->sum();
        $resgates = $this->resgates()->sum('valor');

        return number_format($saldo - $resgates, 2, '.', '');
    }
}
