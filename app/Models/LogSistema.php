<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogSistema extends Model
{
    protected $table = 'logs_sistema';

    protected $fillable = [
        'user_id',
        'secretaria_id',
        'nivel',
        'modulo',
        'acao',
        'descricao',
        'rota',
        'metodo',
        'ip',
        'user_agent',
        'dados',
    ];

    protected $casts = [
        'dados' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function secretaria()
    {
        return $this->belongsTo(Secretaria::class);
    }

    public static function registrar(array $dados): self
    {
        return self::create($dados);
    }
}