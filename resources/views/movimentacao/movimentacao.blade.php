@extends('layouts.app')

@section('content')
    @php
        $isStarting = $movimentacao->isEmpty();
        $step = $isStarting ? 0 : 1;
        $veiculo = $isStarting ? $veiculos->first() : $movimentacao->first()->veiculo;
    @endphp

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap"
        rel="stylesheet">

    <style>
        .font-nunito {
            font-family: 'Nunito', sans-serif;
        }

        .font-sora {
            font-family: 'Sora', sans-serif;
        }

        .field:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }

        .field-error {
            border-color: #ef4444 !important;
            background: #fef2f2 !important;
        }

        .field-error:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, .15) !important;
        }

        .tl-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width .5s ease;
        }

        .tl-fill-0 {
            width: 0%;
            background: #f59e0b;
        }

        .tl-fill-1 {
            width: 50%;
            background: linear-gradient(90deg, #f59e0b, #10b981);
        }

        .tl-fill-2 {
            width: 100%;
            background: #10b981;
        }

        .ring-amber {
            box-shadow: 0 0 0 5px rgba(251, 191, 36, .25);
        }

        .divider {
            display: flex;
            align-items: center;
            gap: .6rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        #errorMsg:not(:empty) {
            margin-top: .5rem;
            padding: .55rem .9rem;
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: .5rem;
            font-size: .82rem;
            color: #b91c1c;
        }

        @media(max-width:639px) {
            .desktop-actions {
                display: none !important;
            }
        }

        @media(min-width:640px) {
            .mobile-footer {
                display: none !important;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #movCard {
            animation: slideUp .38s cubic-bezier(.22, 1, .36, 1) both;
        }
    </style>

    <div
        class="font-nunito min-h-screen bg-gray-100 flex flex-col items-center
            px-3 pt-4 pb-32 sm:pb-10 sm:pt-8 mt-7">

        <div id="movCard" class="w-full max-w-lg bg-white rounded-2xl shadow-lg overflow-hidden">

            {{-- ── HEADER ── --}}
            <div class="{{ $isStarting ? 'bg-amber-50' : 'bg-emerald-50' }} px-5 pt-5 pb-4 border-b border-gray-100">

                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0
                    {{ $isStarting ? 'bg-amber-100' : 'bg-emerald-100' }}">
                        {{ $isStarting ? '🚗' : '📦' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="font-sora text-base font-bold text-gray-900 leading-tight">
                            {{ $isStarting ? 'Saída de Veículo' : 'Entrega de Veículo' }}
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">
                            {{ Auth::user()->name }}
                            @if ($veiculo)
                                &nbsp;·&nbsp; {{ $veiculo->marca ?? '' }} {{ $veiculo->modelo ?? '' }}
                            @endif
                        </p>
                    </div>
                    <span
                        class="shrink-0 px-2.5 py-1 rounded-full text-xs font-bold
                     {{ $isStarting ? 'bg-amber-200 text-amber-900' : 'bg-emerald-200 text-emerald-900' }}">
                        {{ $isStarting ? '🟡 Saindo' : '🟢 Entregando' }}
                    </span>
                </div>

                {{-- Timeline --}}
                <div class="relative flex items-start pt-1">
                    <div class="absolute left-4 right-4 h-1 bg-gray-200 rounded-full" style="top:18px">
                        <div class="tl-fill tl-fill-{{ $step }}"></div>
                    </div>

                    {{-- Nó 1 --}}
                    <div class="flex-1 flex flex-col items-center relative z-10">
                        <div
                            class="w-9 h-9 rounded-full border-4 flex items-center justify-center text-sm font-extrabold
                      {{ $step >= 1
                          ? 'border-emerald-500 bg-emerald-100 text-emerald-700'
                          : 'border-amber-400 bg-white text-amber-600 ring-amber' }}">
                            {{ $step >= 1 ? '✓' : '1' }}
                        </div>
                        <span
                            class="mt-1.5 text-center text-[10px] font-extrabold uppercase tracking-wider
                       {{ $step == 0 ? 'text-amber-600' : 'text-emerald-600' }}">Saída</span>
                        @if (!$isStarting)
                            <span
                                class="text-[10px] text-gray-400">{{ $movimentacao->first()->created_at?->format('H:i') ?? '' }}</span>
                        @endif
                    </div>

                    {{-- Nó 2 --}}
                    <div class="flex-1 flex flex-col items-center relative z-10">
                        <div
                            class="w-9 h-9 rounded-full border-4 flex items-center justify-center text-sm font-extrabold
                      {{ $step == 0
                          ? 'border-gray-200 bg-white text-gray-300'
                          : ($step == 1
                              ? 'border-amber-400 bg-white text-amber-600 ring-amber'
                              : 'border-emerald-500 bg-emerald-100 text-emerald-700') }}">
                            {{ $step == 2 ? '✓' : '2' }}
                        </div>
                        <span
                            class="mt-1.5 text-center text-[10px] font-extrabold uppercase tracking-wider
                       {{ $step == 0 ? 'text-gray-300' : ($step == 1 ? 'text-amber-600' : 'text-emerald-600') }}">
                            Corrida
                        </span>
                        @if (!$isStarting)
                            <span class="text-[10px] text-gray-400">Agora</span>
                        @endif
                    </div>

                    {{-- Nó 3 --}}
                    <div class="flex-1 flex flex-col items-center relative z-10">
                        <div
                            class="w-9 h-9 rounded-full border-4 flex items-center justify-center text-sm font-extrabold
                      {{ $step == 2 ? 'border-emerald-500 bg-emerald-100 text-emerald-700' : 'border-gray-200 bg-white text-gray-300' }}">
                            {{ $step == 2 ? '✓' : '3' }}
                        </div>
                        <span
                            class="mt-1.5 text-center text-[10px] font-extrabold uppercase tracking-wider
                       {{ $step == 2 ? 'text-emerald-600' : 'text-gray-300' }}">Chegada</span>
                        @if ($step == 2)
                            <span class="text-[10px] text-gray-400">{{ now()->format('H:i') }}</span>
                        @endif
                    </div>
                </div>

                <p class="text-center text-xs text-gray-500 mt-4 px-1 leading-relaxed">
                    {{ $isStarting
                        ? 'Preencha os dados abaixo e toque em Iniciar Corrida.'
                        : 'Informe o KM final e toque em Concluir Corrida.' }}
                </p>
            </div>

            {{-- ── FORM ── --}}
            <form id="movForm" method="POST" novalidate
                @if ($movimentacao->isEmpty()) action="{{ route('movimentacao.store') }}"
      @else action="{{ route('movimentacao.update', $movimentacao->first()->id) }}" @endif
                class="px-5 py-5 space-y-5">
                @csrf

                @if ($movimentacao->isEmpty())
                    <input type="hidden" name="data" value="{{ old('data', date('Y-m-d')) }}">
                    <input type="hidden" name="hora" value="{{ old('hora', date('H:i')) }}">
                    <input type="hidden" name="status" value="ativa">
                @else
                    <input type="hidden" name="data_fim" value="{{ old('data_fim', date('Y-m-d')) }}">
                    <input type="hidden" name="hora_fim" value="{{ old('hora_fim', date('H:i')) }}">
                    <input type="hidden" name="status" value="finalizada">
                @endif

                <select name="veiculo_id" id="veiculo_id" hidden>
                    @if ($movimentacao->isEmpty())
                        @foreach ($veiculos as $v)
                            <option selected value="{{ $v->id }}" data-combustivel="{{ $v->combustivel }}"
                                data-km="{{ $v->km_atual }}">
                                {{ $v->placa }} – {{ $v->modelo }}
                            </option>
                        @endforeach
                    @else
                        <option selected value="{{ $movimentacao->first()->veiculo_id }}"
                            data-combustivel="{{ $movimentacao->first()->veiculo->combustivel }}"
                            data-km="{{ $movimentacao->first()->veiculo->km_atual }}"></option>
                    @endif
                </select>

                <select name="user_id" id="motorista_id" hidden>
                    @if ($movimentacao->isEmpty())
                        <option selected value="{{ $user->id }}">{{ $user->name }}</option>
                    @else
                        <option selected value="{{ $movimentacao->first()->user->id }}">
                            {{ $movimentacao->first()->user->name }}</option>
                    @endif
                </select>

                <input id="tipo_combustivel" name="tipo_combustivel" type="hidden"
                    value="{{ old('tipo_combustivel') ?? ($movimentacao->first()->tipo_combustivel ?? '') }}">

                {{-- ── KM ── --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <p class="text-xs font-extrabold uppercase tracking-widest text-gray-400 mb-3">📏 Quilometragem</p>

                    <div class="grid {{ $isStarting ? 'grid-cols-1' : 'grid-cols-2' }} gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1" for="km_inicial">
                                KM Inicial <span class="text-red-500">*</span>
                            </label>
                            @if ($movimentacao->isEmpty())
                                <input id="km_inicial" name="km_inicial" type="number" inputmode="decimal" step="0.1"
                                    value="{{ old('km_inicial') }}" placeholder="Ex: 12345"
                                    class="field w-full rounded-lg border-2 border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 transition-all"
                                    required>
                                <p class="text-xs text-gray-400 mt-1">Leia o hodômetro antes de sair</p>
                            @else
                                <input id="km_inicial" name="km_inicial" type="number" inputmode="decimal" step="0.1"
                                    value="{{ old('km_inicial') ?? ($movimentacao->first()->km_inicial ?? 0) }}"
                                    class="w-full rounded-lg border-2 border-gray-100 bg-gray-100 px-3 py-2.5 text-sm text-gray-400 cursor-not-allowed"
                                    readonly>
                            @endif
                        </div>

                        @if (!$movimentacao->isEmpty())
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1" for="km_final">
                                    KM Final <span class="text-red-500">*</span>
                                </label>
                                <input id="km_final" name="km_final" type="number" inputmode="decimal" step="0.1"
                                    value="{{ old('km_final') ?? '' }}" placeholder="Ex: 12400"
                                    class="field w-full rounded-lg border-2 border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 transition-all @error('km_final') field-error @enderror"
                                    required>
                            </div>
                        @else
                            <input id="km_final" name="km_final" type="number" step="0.1" disabled hidden>
                        @endif
                    </div>

                    <input id="km_rodado" name="km_rodado" type="hidden" value="{{ old('km_rodado', '0.0') }}">

                    @if (!$movimentacao->isEmpty())
                        <div id="kmRodadoPreview" class="mt-2.5 flex items-center gap-2 text-xs text-gray-500">
                            <span>🛣️ KM Rodado:</span>
                            <strong id="kmRodadoVal" class="text-gray-800">—</strong>
                        </div>
                    @endif
                </div>

                {{-- ── ROTA ── --}}
                <div>
                    <div class="divider mb-3">
                        <span
                            class="text-xs font-extrabold uppercase tracking-widest text-gray-400 px-1 whitespace-nowrap">
                            📍 Rota
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1" for="origem">
                                Origem <span class="text-red-500">*</span>
                            </label>
                            <input id="origem" name="origem" type="text"
                                value="{{ old('origem') ?? ($movimentacao->first()->origem ?? '') }}"
                                placeholder="Ex: Sama"
                                class="field w-full rounded-lg border-2 border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 transition-all"
                                required>
                            @error('origem')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1" for="destino">
                                Destino <span class="text-red-500">*</span>
                            </label>
                            <input id="destino" name="destino" type="text"
                                value="{{ old('destino') ?? ($movimentacao->first()->destino ?? '') }}"
                                placeholder="Ex: Paço"
                                class="field w-full rounded-lg border-2 border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 transition-all"
                                required>
                            @error('destino')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── OBSERVAÇÕES ── --}}
                <div>
                    <div class="divider mb-3">
                        <span
                            class="text-xs font-extrabold uppercase tracking-widest text-gray-400 px-1 whitespace-nowrap">
                            📝 Observações
                        </span>
                    </div>
                    <textarea id="observacoes" name="observacao" rows="3" placeholder="Opcional — informe qualquer dado relevante"
                        class="field w-full rounded-lg border-2 border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-900 transition-all resize-none">{{ old('observacoes') ?? ($movimentacao->first()->observacoes ?? '') }}</textarea>
                </div>

                <div id="errorMsg" role="alert" aria-live="polite"></div>

                {{-- ── DESKTOP ACTIONS ── --}}
                <div class="desktop-actions flex items-center justify-between pt-1 gap-3">
                    <p class="text-xs text-gray-400"><span class="text-red-400">*</span> campos obrigatórios</p>
                    <div class="flex items-center gap-2">
                        @if (!$movimentacao->isEmpty())
                            <a href="{{ route('movimentacao.cancelar', ['id' => $movimentacao->first()->id, 'veiculoId' => $movimentacao->first()->veiculo_id]) }}"
                                class="px-4 py-2 rounded-lg border border-red-300 text-red-500 text-sm font-bold hover:bg-red-50 transition-all">
                                Cancelar
                            </a>
                        @endif
                        @if ($movimentacao->isEmpty())
                            <button type="submit"
                                class="px-5 py-2 rounded-lg bg-amber-400 hover:bg-amber-500 text-white text-sm font-bold
                     shadow-md shadow-amber-200 transition-all active:scale-95 border-none cursor-pointer"
                                style="background-color: #f97316; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.35);">
                                🚗 Iniciar Corrida
                            </button>
                        @else
                            <button type="submit"
                                class="px-5 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold
                     shadow-md shadow-emerald-200 transition-all active:scale-95 border-none cursor-pointer"
                                style="background-color: #f97316; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.35);">
                                🏁 Concluir Corrida
                            </button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- ── MOBILE FOOTER ── --}}
    <div class="mobile-footer fixed inset-x-0 bottom-0 z-50 bg-white bg-opacity-95 border-t border-gray-200 px-4 py-3"
        style="padding-bottom: max(.75rem, env(safe-area-inset-bottom))">

        <div class="flex gap-2.5 max-w-lg mx-auto">

            @if (!$movimentacao->isEmpty())
                <a href="{{ route('movimentacao.cancelar', ['id' => $movimentacao->first()->id, 'veiculoId' => $movimentacao->first()->veiculo_id]) }}"
                    class="flex-1 text-center py-3.5 rounded-xl border border-red-300 text-red-500 text-sm font-bold transition-all active:scale-95">
                    ✕ Cancelar
                </a>
            @endif

            @if ($movimentacao->isEmpty())
                <button onclick="document.getElementById('movForm').submit()"
                    class="flex-1 py-3.5 rounded-xl text-white text-sm font-bold
           shadow-lg transition-all active:scale-95 border-none cursor-pointer"
                    style="background-color: #f97316; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.35);">
                    🚗 Iniciar Corrida
                </button>
            @else
                <button onclick="document.getElementById('movForm').submit()"
                    class="flex-1 py-3.5 rounded-xl bg-orange-500 text-white text-sm font-bold
               shadow-lg shadow-orange-200 transition-all active:scale-95 border-none cursor-pointer"
                    style="background-color: #f97316; box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.35);">
                    🏁 Concluir Corrida
                </button>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /* scroll centralizado no card ao carregar */
                var card = document.getElementById('movCard');
                if (card) {
                    setTimeout(function() {
                        card.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 150);
                }

                var selVeiculo = document.getElementById('veiculo_id');
                var kmInicialFld = document.getElementById('km_inicial');
                var tipoFld = document.getElementById('tipo_combustivel');
                var kmFinalFld = document.getElementById('km_final');
                var kmRodadoFld = document.getElementById('km_rodado');
                var kmRodadoVal = document.getElementById('kmRodadoVal');
                var errorMsg = document.getElementById('errorMsg');

                function applyVehicle(opt) {
                    if (!opt) return;
                    var km = opt.getAttribute('data-km') || '';
                    var comb = opt.getAttribute('data-combustivel') || '';
                    if (kmInicialFld && km) kmInicialFld.value = km;
                    if (tipoFld && comb) tipoFld.value = comb;
                    recalc();
                }

                if (selVeiculo) {
                    selVeiculo.addEventListener('change', function() {
                        applyVehicle(this.options[this.selectedIndex]);
                    });
                    applyVehicle(selVeiculo.querySelector('option[selected]') || selVeiculo.options[0]);
                }

                function recalc() {
                    if (!kmInicialFld || !kmFinalFld || !kmRodadoFld) return;
                    var a = parseFloat(kmInicialFld.value);
                    var b = parseFloat(kmFinalFld.value);

                    if (!kmFinalFld.value || isNaN(b)) {
                        kmRodadoFld.value = '0.0';
                        if (kmRodadoVal) kmRodadoVal.textContent = '—';
                        if (errorMsg) errorMsg.textContent = '';
                        return;
                    }
                    if (isNaN(a)) {
                        if (errorMsg) errorMsg.textContent = 'Informe um KM inicial válido.';
                        return;
                    }
                    var diff = b - a;
                    if (diff < 0) {
                        kmRodadoFld.value = '';
                        if (kmRodadoVal) kmRodadoVal.textContent = '⚠️ inválido';
                        if (errorMsg) errorMsg.textContent = 'KM Final não pode ser menor que KM Inicial.';
                    } else {
                        kmRodadoFld.value = diff.toFixed(1);
                        if (kmRodadoVal) kmRodadoVal.textContent = diff.toFixed(1) + ' km';
                        if (errorMsg) errorMsg.textContent = '';
                    }
                }

                if (kmInicialFld) kmInicialFld.addEventListener('input', recalc);
                if (kmFinalFld) kmFinalFld.addEventListener('input', recalc);
                recalc();
            });
        </script>
    @endpush
