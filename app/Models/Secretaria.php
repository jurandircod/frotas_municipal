<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class secretaria extends Model
{
    protected $table = 'secretarias';

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
