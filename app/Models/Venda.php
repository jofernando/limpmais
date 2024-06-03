<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'tipo_quantidade',
        'quantidade',
        'folguista',
        'prazo'
    ];

    /**
     * Get all of the motoristaVenda for the Venda
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function motoristaVenda(): HasMany
    {
        return $this->hasMany(MotoristaVenda::class);
    }

    /**
     * Get the produto that owns the Venda
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
