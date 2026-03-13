<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Motorista extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
        'email',
        'cnh',
        'categoria',
        'validade_cnh',
        'data_nascimento',
        'endereco',
        'status',
    ];

    protected $casts = [
        'validade_cnh' => 'date',
        'data_nascimento' => 'date',
    ];

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'motorista_id');
    }
}