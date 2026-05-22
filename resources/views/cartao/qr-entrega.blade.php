@extends('layouts.app')

@section('title', 'QR Code Entrega')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-2xl shadow p-6">

    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-800">
            QR Code de Entrega
        </h1>

        <p class="text-gray-500 mt-2">
            {{ $cartao->nome_veiculo }}
        </p>

        <p class="text-sm text-gray-400">
            {{ $cartao->placa }}
        </p>
    </div>

    <div class="flex justify-center mt-6">
        <img
            src="{{ asset('storage/' . $cartao->cartao_qr_retirada) }}"
            class="w-72 h-72 border rounded-xl p-3 bg-white">
    </div>

    <div class="mt-6 text-center">
        <a href="{{ asset('storage/' . $cartao->cartao_qr_retirada) }}"
            download
            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Baixar QR Code
        </a>
    </div>

</div>
@endsection