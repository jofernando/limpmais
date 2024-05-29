<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'valor',
        'data',
        'duplicata_id',
        'metodo_pagamento_id',
    ];


    public function duplicata(): BelongsTo
    {
        return $this->belongsTo(Duplicata::class);
    }

    public function metodoPagamento(): BelongsTo
    {
        return $this->belongsTo(MetodoPagamento::class);
    }
}
