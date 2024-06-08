<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'data',
        'toneladas',
        'sacas',
        'motorista_id',
        'veiculo_id',
    ];

    /**
     * Get the contrato that owns the Entrega
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Get the motorista that owns the Entrega
     */
    public function motorista(): BelongsTo
    {
        return $this->belongsTo(Motorista::class);
    }

    /**
     * Get the veiculo that owns the Entrega
     */
    public function veiculo(): BelongsTo
    {
        return $this->belongsTo(Veiculo::class);
    }
}
