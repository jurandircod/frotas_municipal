@extends('layouts.app')

@section('title', 'Cartão')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow p-6">

    <h1 class="text-2xl font-bold mb-4">
        Cartão {{ $cartao->numero_cartao }}
    </h1>

    <div class="space-y-2">
        <p>
            <strong>Veículo:</strong>
            {{ $cartao->nome_veiculo }}
        </p>

        <p>
            <strong>Placa:</strong>
            {{ $cartao->placa }}
        </p>

        <p>
            <strong>Horímetro:</strong>
            {{ $cartao->horimetro }}
        </p>

        <p>
            <strong>Aumento Horímetro:</strong>
            {{ $cartao->aumento_horimetro }}
        </p>
    </div>

</div>
@endsection