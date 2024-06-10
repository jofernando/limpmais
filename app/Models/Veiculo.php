<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = ['placa', 'observacao'];

    /**
     * Get all of the duplicatas for the Veiculo
     */
    public function duplicatas(): HasMany
    {
        return $this->hasMany(Duplicata::class);
    }
}
