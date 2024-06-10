<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'tamanho_id',
        'cor_id',
        'quantidade',
        'duplicata_id',
    ];

    /**
     * Get the tamanho that owns the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tamanho(): BelongsTo
    {
        return $this->belongsTo(Tamanho::class);
    }

    /**
     * Get the cor that owns the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cor(): BelongsTo
    {
        return $this->belongsTo(Cor::class);
    }

    /**
     * Get the duplicata that owns the Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function duplicata(): BelongsTo
    {
        return $this->belongsTo(Duplicata::class);
    }
}
