@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">Motorista - <span class="text-blue-600">{{ Auth::user()->name }}</span></h1>
                    <p class="text-sm text-gray-500 mt-1">Diário de bordo — preencha os dados da saída</p>
                </div>

                <form id="movForm" method="POST"
                    @if ($movimentacao->isEmpty()) action="{{ route('movimentacao.store') }}" @else action="{{ route('movimentacao.update', $movimentacao->first()->id) }}" @endif
                    class="px-6 py-6 space-y-6" novalidate>
                    @csrf

                    <!-- row: date + time -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" hidden>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700" hidden>Data</span>
                            <input name="data" id="data" type="date" value="{{ old('data', date('Y-m-d')) }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true" hidden>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700" hidden>Hora</span>
                            <input name="hora" id="hora" type="time" value="{{ old('hora', date('H:i')) }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true" hidden>
                        </label>
                    </div>

                    @php
                        // valor padrão: 'ativa' (mude aqui se quiser outro default)
                        if ($movimentacao->isEmpty()) {
                            $currentStatus = old('status', 'ativa');
                        } else {
                            $currentStatus = 'finalizada';
                        }
                        $badgeClasses = match ($currentStatus) {
                            'cancelada' => 'bg-red-100 text-red-800',
                            'finalizada' => 'bg-green-100 text-yellow-800',
                            default => 'bg-yellow-100 text-green-800', // 'ativa' e fallback
                        };

                        $badgeLabel = match ($currentStatus) {
                            'cancelada' => 'Cancelada',
                            'finalizada' => 'Em movimentação',
                            default => 'Ativa',
                        };
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block col-span-1 sm:col-span-2">
                            <span class="text-sm font-medium text-gray-700">Status</span>

                            {{-- hidden para envio ao backend --}}
                            <input type="hidden" name="status" id="status" value="{{ $currentStatus }}">

                            {{-- badge visual não-editável --}}
                            <div class="mt-2">
                                <span id="statusBadge"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}"
                                    role="status" aria-live="polite" aria-atomic="true">
                                    {{ $badgeLabel }}
                                </span>
                            </div>

                            @error('status')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <!-- row: veiculo + motorista -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700"
                                @if (isset($veiculoId)) hidden @endif>Veículo</span>

                            <select name="veiculo_id" id="veiculo_id" @if (isset($veiculoId)) hidden @endif
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                                @if ($movimentacao->isEmpty())
                                    @foreach ($veiculos as $v)
                                        <option selected value="{{ $v->id }}"
                                            data-combustivel="{{ $v->combustivel }}" data-km="{{ $v->km_atual }}">
                                            {{ $v->placa }} - {{ $v->modelo }}
                                        </option>
                                    @endforeach
                                @else
                                    <option selected value="{{ $movimentacao->first()->veiculo_id }}"
                                        data-combustivel="{{ $movimentacao->first()->veiculo->combustivel }}"
                                        data-km="{{ $movimentacao->first()->veiculo->km_atual }}">
                                        {{ $movimentacao->first()->veiculo->placa }} -
                                        {{ $movimentacao->first()->veiculo->modelo }}
                                    </option>
                                @endif
                            </select>
                            @error('veiculo_id')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>

                        <label class="block" @if (isset($veiculoId)) hidden @endif>
                            <span class="text-sm font-medium text-gray-700"
                                @if (isset($veiculoId)) hidden @endif>Motorista</span>
                            <select name="user_id" id="motorista_id" @if (isset($veiculoId)) hidden @endif
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true">
                                @if ($movimentacao->isEmpty())
                                    <option selected value="{{ $user->id }}"
                                        {{ old('users_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}</option>
                                @else
                                    <option selected value="{{ $movimentacao->first()->user->id }}"
                                        {{ old('users_id') == $movimentacao->first()->user->id ? 'selected' : '' }}>
                                        {{ $movimentacao->first()->user->name }}</option>
                                @endif
                            </select>
                            @error('user_id')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <!-- combustivel -->
                    <div @if (isset($veiculoId)) hidden @endif>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Tipo de Combustível</span>
                            @if ($movimentacao->isEmpty())
                                <input
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    type="text" id ="tipo_combustivel" name="tipo_combustivel"
                                    value="{{ old('tipo_combustivel') }}">
                            @else
                                <input
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    type="text" id ="tipo_combustivel" name="tipo_combustivel"
                                    value="{{ old('tipo_combustivel') ?? $movimentacao->first()->tipo_combustivel }}">
                            @endif
                            @error('tipo_combustivel')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <!-- row: km -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">KM Inicial</span>
                            @if ($movimentacao->isEmpty())
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" pattern="^\d+(\.\d{1,2})?$"
                                    type="number" step="0.1" value="{{ old('km_inicial') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" aria-describedby="kmInitHelp">
                            @else
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" pattern="^\d+(\.\d{1,2})?$"
                                    disabled type="number" step="0.1"
                                    value="{{ old('km_inicial') ?? ($movimentacao->first()->km_inicial ?? 0) }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" aria-describedby="kmInitHelp">
                            @endif
                            <small id="kmInitHelp" class="text-xs text-gray-400">Ex: 12345.5</small>
                            @error('km_inicial')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>

                        <label class="block">
                            @if ($movimentacao->isEmpty())
                                {{-- KM Final deve estar editável ao criar --}}
                                <input name="km_final" hidden id="km_final" inputmode="decimal"
                                    pattern="^\d+(\.\d{1,2})?$" type="number" disabled step="0.1"
                                    value="{{ old('km_final') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true">
                            @else
                                <span class="text-sm font-medium text-gray-700">KM Final</span>
                                <input name="km_final" id="km_final" inputmode="decimal" pattern="^\d+(\.\d{1,2})?$"
                                    type="number" step="0.1"
                                    value="{{ old('km_final') ?? $movimentacao->first()->km_final }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true">
                            @endif
                            @error('km_final')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>

                        <label class="">
                            @if ($movimentacao->isEmpty())
                                <input name="km_rodado" hidden id="km_rodado" type="number" value="0.0"
                                    class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm py-3 px-3" readonly
                                    aria-readonly="true">
                            @else
                                <span class="text-sm font-medium text-gray-700">KM Rodado</span>
                                <input name="km_rodado" id="km_rodado" type="number"
                                    value="{{ old('km_rodado') ?? '0.0' }}"
                                    class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm py-3 px-3" readonly
                                    aria-readonly="true">
                            @endif
                            @error('km_rodado')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <!-- origem / destino -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Origem</span>
                            <input name="origem" id="origem" type="text" value="{{ old('origem') ?? 'SAMA' }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true">
                            @error('origem')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Destino</span>
                            @if ($movimentacao->isEmpty())
                                <input name="destino" id="destino" type="text" value="{{ old('destino') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true">
                            @else
                                <input name="destino" id="destino" type="text"
                                    value="{{ old('destino') ?? $movimentacao->first()->destino }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true">
                            @endif
                            @error('destino')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Observações</span>
                            <textarea name="observacoes" id="observacoes" rows="3"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="Opcional">{{ old('observacoes') }}</textarea>
                            @error('observacao')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>
                    </div>

                    <!-- validation / messages -->
                    <div id="errorMsg" class="text-sm text-red-600" role="alert" aria-live="polite"></div>

                    <!-- desktop action row -->
                    <div class="sm:flex items-center justify-between mt-2">
                        <div class="text-sm text-gray-500">Campos com * são obrigatórios</div>
                        <div class="flex gap-3">
                            @if ($movimentacao->isEmpty())
                                <button id="submitBtn" type="submit"
                                    class="px-4 py-2 rounded-lg bg-blue-600 text-white shadow-sm">Iniciar Corrida</button>
                            @else
                                <a href="{{ route('movimentacao.cancelar', $movimentacao->first()->id) }}"
                                    class="px-4 py-2 rounded-lg border border-red-300 text-red-700">Cancelar</a>
                                <button id="submitBtn" type="submit"
                                    class="px-4 py-2 rounded-lg bg-blue-600 text-white shadow-sm">Concluir
                                    Corrida</button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selVeiculo = document.getElementById('veiculo_id');
            const kmInicialField = document.getElementById('km_inicial');
            const tipoField = document.getElementById('tipo_combustivel');
            const kmFinal = document.getElementById('km_final');
            const kmRodado = document.getElementById('km_rodado');
            const errorMsg = document.getElementById('errorMsg');

            if (!selVeiculo) return;

            // função que atualiza km inicial e tipo a partir do option selecionado (se houver)
            function applySelectedVehicle(option) {
                if (!option) return;
                const km = option.getAttribute('data-km') || '';
                const combustivel = option.getAttribute('data-combustivel') || '';
                if (kmInicialField && km !== '') kmInicialField.value = km;
                if (tipoField) tipoField.value = combustivel;
                // se já houver km_final, recalcula km_rodado
                recalcKmRodado();
            }

            // handler quando o usuário muda o select
            selVeiculo.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                applySelectedVehicle(opt);
            });

            // tenta obter o option selecionado no HTML, mesmo que seja marcado com `selected`
            (function applyInitialSelection() {
                let initialOpt = null;

                // 1) Se o select já tem um index selecionado válido, usa esse
                if (selVeiculo.selectedIndex >= 0) {
                    initialOpt = selVeiculo.options[selVeiculo.selectedIndex];
                }

                // 2) Se não encontrou (ou para segurança), procura por option[selected] no DOM
                if ((!initialOpt || initialOpt.value === '') && selVeiculo.querySelector('option[selected]')) {
                    initialOpt = selVeiculo.querySelector('option[selected]');
                }

                // 3) fallback: seleciona a primeira option que tenha data-km (útil se você acidentalmente deixou todas 'selected')
                if ((!initialOpt || initialOpt.value === '')) {
                    const withDataKm = selVeiculo.querySelector('option[data-km]');
                    if (withDataKm && withDataKm.value !== '') initialOpt = withDataKm;
                }

                applySelectedVehicle(initialOpt);
            })();

            // --- KM rodado (mesma lógica do seu formulário) ---
            function recalcKmRodado() {
                if (!kmInicialField || !kmFinal || !kmRodado) return;

                const a = parseFloat(kmInicialField.value === '' ? NaN : kmInicialField.value);
                const b = parseFloat(kmFinal.value === '' ? NaN : kmFinal.value);

                if (kmFinal.value === '' || isNaN(b)) {
                    kmRodado.value = '0.0';
                    errorMsg && (errorMsg.textContent = '');
                    kmFinal.removeAttribute('aria-invalid');
                    return;
                }

                if (isNaN(a)) {
                    kmRodado.value = '';
                    errorMsg && (errorMsg.textContent = 'Informe um KM inicial válido.');
                    return;
                }

                const diff = b - a;
                if (diff < 0) {
                    kmRodado.value = '';
                    errorMsg && (errorMsg.textContent = 'KM Final não pode ser menor que KM Inicial.');
                    kmFinal.setAttribute('aria-invalid', 'true');
                } else {
                    kmRodado.value = diff.toFixed(1);
                    errorMsg && (errorMsg.textContent = '');
                    kmFinal.removeAttribute('aria-invalid');
                }
            }

            // liga eventos para calcular
            if (kmInicialField) kmInicialField.addEventListener('input', recalcKmRodado);
            if (kmFinal) kmFinal.addEventListener('input', recalcKmRodado);

            // se quiser garantir que o recálculo rode ao carregar (quando já houver valores preenchidos)
            recalcKmRodado();
        });
    </script>
@endpush
