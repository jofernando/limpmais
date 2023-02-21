<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Duplicata extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'vencimento',
        'observacao',
        'cliente_id',
        'quitada',
    ];

    protected $casts = [
        'vencimento' => 'datetime',
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

    public function getStatusAttribute(): string
    {
        if($this->quitada) return 'pago';
        if($this->vencimento < now()) return 'vencido';
        else return 'pendente';
    }
}
