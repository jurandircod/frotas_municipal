<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cartao extends Model
{
    protected $table = 'cartoes';

    protected $fillable = [
        'nome_veiculo',
        'placa',
        'numero_cartao',
        'horimetro',
        'aumento_horimetro',
    ];

    public function retiradas()
    {
        return $this->hasMany(Retirada::class, 'cartao_id');
    }
}