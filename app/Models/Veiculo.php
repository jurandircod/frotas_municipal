<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'ano',
        'cor',
        'tipo_veiculo_id',
        'combustivel',
        'km_atual',
        'status',
    ];

    protected $casts = [
        'km_atual' => 'decimal:1',
        'ano' => 'integer',
    ];

    public function tipoVeiculo()
    {
        return $this->belongsTo(TipoVeiculo::class, 'tipo_veiculo_id');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'veiculo_id');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($veiculo) {
            $url = route('movimentacao.veiculo', $veiculo->id);

            $veiculo->update([
                'veiculo_qr_code' => QrCode::format('png')
                    ->size(200)
                    ->margin(1)
                    ->errorCorrection('H')
                    ->generate($url)
            ]);
        });
    }
}
