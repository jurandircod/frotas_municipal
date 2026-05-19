<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ferramenta extends Model
{
    protected $table = 'ferramentas';

    protected $fillable = [
        'nome',
        'descricao',
    ];

    public function retiradas()
    {
        return $this->hasMany(Retirada::class, 'ferramenta_id');
    }
}