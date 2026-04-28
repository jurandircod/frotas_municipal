<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
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
        'veiculo_qr_code',
    ];

    public function tipoVeiculo()
    {
        return $this->belongsTo(TipoVeiculo::class, 'tipo_veiculo_id');
    }

    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao::class, 'veiculo_id');
    }

    protected static function booted()
    {
        static::created(function ($veiculo) {
            $veiculo->gerarQrCode();
        });
    }

    public function gerarQrCode(): string
    {
        $url = route('movimentacao.withVeiculo', [
            'veiculoId' => $this->id
        ], true);
        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($url);

        $path = "qrcodes/veiculos/veiculo-{$this->id}.svg";

        Storage::disk('public')->put($path, $svg);

        $this->forceFill([
            'veiculo_qr_code' => $path,
        ])->saveQuietly();

        return $path;
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        return $this->veiculo_qr_code
            ? asset('storage/' . $this->veiculo_qr_code)
            : null;
    }
}
