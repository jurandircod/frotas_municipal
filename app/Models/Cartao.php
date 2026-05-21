<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Cartao extends Model
{
    protected $table = 'cartoes';
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_INATIVO = 'inativo';

    protected $fillable = [
        'nome_veiculo',
        'placa',
        'numero_cartao',
        'horimetro',
        'aumento_horimetro',
        'status'
    ];

    protected static function booted()
    {
        static::created(function ($cartao) {
            $cartao->gerarQrCodes();
        });
    }

    public function gerarQrCodes(): void
    {
        $this->gerarQrCodeRetirada();
        $this->gerarQrCodeEntrega();
    }

    public function gerarQrCodeRetirada(): string
    {
        $url = route('retirada.cartao.retirada.automatico', [
            'id' => $this->id
        ], true);

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($url);

        $path = "qrcodes/cartoes/cartao-{$this->id}-retirada.svg";

        Storage::disk('public')->put($path, $svg);

        $this->forceFill([
            'cartao_qr_retirada' => $path,
        ])->saveQuietly();

        return $path;
    }

    public function gerarQrCodeEntrega(): string
    {
        $url = route('retirada.cartao.entrega.automatico', [
            'id' => $this->id
        ], true);

        $svg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate($url);

        $path = "qrcodes/cartoes/cartao-{$this->id}-entrega.svg";

        Storage::disk('public')->put($path, $svg);

        $this->forceFill([
            'cartao_qr_entrega' => $path,
        ])->saveQuietly();

        return $path;
    }

    public function getQrRetiradaUrlAttribute(): ?string
    {
        return $this->cartao_qr_retirada
            ? asset('storage/' . $this->cartao_qr_retirada)
            : null;
    }

    public function getQrEntregaUrlAttribute(): ?string
    {
        return $this->cartao_qr_entrega
            ? asset('storage/' . $this->cartao_qr_entrega)
            : null;
    }
}
