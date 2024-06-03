<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotoristaVenda extends Model
{
    use HasFactory;

    protected $table = 'motorista_venda';

    protected $fillable = [
        'motorista_id',
        'venda_id',
        'placa',
    ];
}
