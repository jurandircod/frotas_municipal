<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimentacao extends Model
{
    use HasFactory;

    protected $table = 'movimentacoes';
    
    protected $fillable = [
        'data',
        'hora',
        'veiculo_id',
        'user_id',
        'tipo_combustivel',
        'km_inicial',
        'km_final',
        'km_rodado',
        'origem',
        'destino',
        'observacao',
        'status',
        'data_fim',
        'hora_fim',
    ];

    protected $casts = [
        'data' => 'date',
        'hora' => 'string',
        'km_inicial' => 'decimal:1',
        'km_final' => 'decimal:1',
        'km_rodado' => 'decimal:1',
    ];

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class, 'veiculo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Opção prática: calcular km_rodado automaticamente se não informado.
     */
    public function calculateKmRodado(): ?float
    {
        if (is_null($this->km_inicial) || is_null($this->km_final)) {
            return null;
        }
        return round((float)$this->km_final - (float)$this->km_inicial, 1);
    }
}