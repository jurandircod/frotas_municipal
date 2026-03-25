<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class secretaria extends Model
{
    protected $table = 'secretarias';

    protected $fillable = [
        'nome',
        'descricao',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
