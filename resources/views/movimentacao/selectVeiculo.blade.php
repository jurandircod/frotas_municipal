@extends('layouts.app')
@section('content')
@section('content')
    <div class="max-w-lg">
        <p class="text-sm text-gray-500 mb-6">
            Selecione um veículo para prosseguir com a movimentação.
        </p>
        <div class="mb-6">
            <label for="veiculo_id" class="block text-sm font-medium text-gray-700 mb-1">
                Veículo
            </label>

            <div class="relative">
                {{-- ícone esquerdo --}}
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 17H5a2 2 0 01-2-2V9a2 2 0 012-2h1l2-3h8l2 3h1a2 2 0 012 2v6a2 2 0 01-2 2h-3m-6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>

                <select id="veiculo_id" name="veiculo_id" required
                    class="block w-full rounded-lg border border-gray-200 bg-white py-2.5 pl-10 pr-10
                           text-sm text-gray-800 shadow-sm
                           focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100
                           @error('veiculo_id') border-red-400 @enderror">
                    <option value="" disabled selected>Selecione um veículo...</option>
                    @foreach ($veiculos as $veiculo)
                        <option value="{{ $veiculo->id }}" {{ old('veiculo_id') == $veiculo->id ? 'selected' : '' }}>
                            {{ $veiculo->modelo }} — {{ $veiculo->placa }}
                        </option>
                    @endforeach
                </select>

                {{-- seta direita --}}
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            @error('veiculo_id')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selVeiculo = document.getElementById('veiculo_id');
        
        selVeiculo.onchange = function() {
            const veiculoId = selVeiculo.value;
            if (veiculoId) {
                // Construa a URL manualmente baseada na sua rota
                window.location.href = '/movimentacao/veiculo/' + veiculoId;
            }
        };
    });
</script>
