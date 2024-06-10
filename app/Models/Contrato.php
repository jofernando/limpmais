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
        'vigencia',
        'produto_id',
        'n_contrato',
        'observacao',
        'cor_id',
        'tamanho_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    protected $appends = ['saldo_receber'];

    /**
     * Get the fornecedor that owns the Contrato
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function cor(): BelongsTo
    {
        return $this->belongsTo(Cor::class);
    }

    public function tamanho(): BelongsTo
    {
        return $this->belongsTo(Tamanho::class);
    }

    /**
     * Get the produto that owns the Contrato
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Get all of the entregas for the Contrato
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

    public function getResgatadaAttribute()
    {
        if ($this->sacas != null) {
            return $this->entregas()->sum('sacas');
        } elseif ($this->toneladas != null) {
            return $this->entregas()->sum('toneladas');
        }
        return 0;
    }

    public function getRestanteAttribute()
    {
        if ($this->sacas != null) {
            return $this->sacas - $this->entregas()->sum('sacas');
        } elseif ($this->toneladas != null) {
            return $this->toneladas - $this->entregas()->sum('toneladas');
        }
        return 0;
    }
}
