<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Retirada extends Model
{
    protected $table = 'retiradas';

    protected $fillable = [
        'cartao_id',
        'ferramenta_id',
        'retirada_autorizada_por',
        'entrega_autorizada_por',
        'datahora_retirada',
        'datahora_entrega',
        'categoria',
        'nome_generico',
        'user_id',
        'status'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartao()
    {
        return $this->belongsTo(Cartao::class);
    }

    public function ferramenta()
    {
        return $this->belongsTo(Ferramenta::class);
    }
}
