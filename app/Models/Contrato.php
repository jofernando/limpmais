<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contrato extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'pago',
        'tipo',
        'toneladas',
        'sacas',
        'data',
        'observacao'
    ];

    protected $casts = [
        'valor' => 'decimal:2'
    ];

    protected $appends = ['saldo_receber'];

    /**
     * Get the fornecedor that owns the Contrato
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    /**
     * Get all of the entregas for the Contrato
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class);
    }

    public function getSaldoReceberAttribute()
    {
        $unitario = $this->valor / $this->getAttribute($this->tipo);
        $entregue = $this->entregas()->sum($this->tipo);
        return $this->valor - ($entregue * $unitario);
    }
}
