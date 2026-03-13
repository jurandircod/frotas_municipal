<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoVeiculo extends Model
{
    use HasFactory;

    protected $table = 'tipos_veiculos';

    protected $fillable = [
        'nome',
    ];

    public function veiculos()
    {
        return $this->hasMany(Veiculo::class, 'tipo_veiculo_id');
    }
}