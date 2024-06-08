<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetodoPagamento extends Model
{
    use HasFactory;

    protected $fillable = ['tipo'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['valor_recebido'];

    public function getValorRecebidoAttribute()
    {
        $valor = $this->pagamentos->sum('valor');

        return number_format($valor, 2, '.', '');
    }

    /**
     * Get all of the pagamentos for the MetodoPagamento
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class);
    }
}
