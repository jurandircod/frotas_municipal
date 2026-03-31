@extends('layouts.app')
@section('content')

    @php
        $isStarting = $movimentacao->isEmpty();
        $mode = $isStarting ? 'saindo' : 'entregando';
        // step: 0 = aguardando, 1 = em corrida, 2 = finalizado
        $step = $isStarting ? 0 : 1;
    @endphp

    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap');

        .mov-root * {
            box-sizing: border-box;
        }

        .mov-root {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            background: #f4f6fa;
            padding: 1.5rem 1rem 7rem;
        }

        /* ── CARD ── */
        .mov-card {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .08);
            overflow: hidden;
        }

        /* ── HEADER ── */
        .mov-header {
            padding: 1.5rem 1.75rem 1.25rem;
            border-bottom: 1px solid #f0f0f3;
            background: #fafbff;
        }

        .mov-header-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: .75rem;
        }

        .mov-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .mov-avatar.yellow {
            background: #fef3c7;
        }

        .mov-avatar.green {
            background: #d1fae5;
        }

        .mov-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }

        .mov-meta {
            font-size: .8rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .mov-meta span {
            color: #3b82f6;
            font-weight: 600;
        }

        .mov-badge {
            margin-left: auto;
            padding: .3rem .9rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .mov-badge.yellow {
            background: #fef3c7;
            color: #92400e;
        }

        .mov-badge.green {
            background: #d1fae5;
            color: #065f46;
        }

        /* ── PROGRESS TIMELINE ── */
        .timeline-wrap {
            padding: 1.25rem 1.75rem 0;
        }

        .timeline {
            display: flex;
            align-items: center;
            position: relative;
            gap: 0;
        }

        /* linha de fundo (cinza) */
        .timeline-track {
            position: absolute;
            top: 50%;
            left: 24px;
            right: 24px;
            height: 4px;
            background: #e5e7eb;
            border-radius: 4px;
            transform: translateY(-50%);
            z-index: 0;
        }

        /* linha de progresso (colorida) */
        .timeline-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 4px;
            transition: width .5s ease;
        }

        .timeline-fill.step0 {
            width: 0%;
            background: #f59e0b;
        }

        .timeline-fill.step1 {
            width: 50%;
            background: linear-gradient(90deg, #f59e0b, #10b981);
        }

        .timeline-fill.step2 {
            width: 100%;
            background: #10b981;
        }

        /* nós */
        .tl-node {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .tl-dot {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 3px solid #e5e7eb;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            transition: all .3s ease;
            position: relative;
        }

        .tl-dot.done {
            border-color: #10b981;
            background: #d1fae5;
        }

        .tl-dot.active {
            border-color: #f59e0b;
            background: #fef3c7;
            box-shadow: 0 0 0 4px #fef3c740;
        }

        .tl-dot.pending {
            opacity: .5;
        }

        .tl-label {
            margin-top: .45rem;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #9ca3af;
            text-align: center;
        }

        .tl-label.active {
            color: #d97706;
        }

        .tl-label.done {
            color: #059669;
        }

        .tl-time {
            font-size: .65rem;
            color: #b0b7c3;
            margin-top: 1px;
        }

        /* hint abaixo da timeline */
        .timeline-hint {
            font-size: .78rem;
            color: #6b7280;
            padding: .6rem 0 1rem;
            text-align: center;
        }

        /* ── FORM ── */
        .mov-form {
            padding: 1.5rem 1.75rem;
        }

        .form-row {
            display: grid;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .form-row.cols2 {
            grid-template-columns: 1fr 1fr;
        }

        .form-row.cols3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        @media(max-width:500px) {

            .form-row.cols2,
            .form-row.cols3 {
                grid-template-columns: 1fr;
            }
        }

        .form-label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: .4rem;
        }

        .form-label .req {
            color: #ef4444;
        }

        .form-input {
            width: 100%;
            padding: .7rem .9rem;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            color: #111827;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px #3b82f620;
        }

        .form-input.readonly-look {
            background: #f9fafb;
            color: #6b7280;
            border-color: #f0f0f3;
            cursor: default;
        }

        .form-input::placeholder {
            color: #9ca3af;
        }

        textarea.form-input {
            resize: vertical;
            min-height: 80px;
        }

        .form-hint {
            font-size: .7rem;
            color: #9ca3af;
            margin-top: 3px;
        }

        /* KM card com destaque */
        .km-group {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
        }

        .km-group-title {
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
            margin-bottom: .75rem;
        }

        .error-text {
            font-size: .78rem;
            color: #ef4444;
            margin-top: .3rem;
        }

        /* ── DIVIDER ── */
        .form-divider {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin: 1.25rem 0;
        }

        .form-divider span {
            font-size: .75rem;
            color: #9ca3af;
            white-space: nowrap;
        }

        .form-divider::before,
        .form-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #f0f0f3;
        }

        /* ── ACTIONS ── */
        .form-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.5rem;
            gap: .75rem;
        }

        .form-actions-hint {
            font-size: .75rem;
            color: #9ca3af;
        }

        .btn {
            padding: .65rem 1.4rem;
            border-radius: 10px;
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            transition: all .18s ease;
            text-decoration: none;
        }

        .btn:active {
            transform: scale(.97);
        }

        .btn-yellow {
            background: #f59e0b;
            color: #fff;
            box-shadow: 0 2px 8px #f59e0b44;
        }

        .btn-yellow:hover {
            background: #d97706;
        }

        .btn-green {
            background: #10b981;
            color: #fff;
            box-shadow: 0 2px 8px #10b98144;
        }

        .btn-green:hover {
            background: #059669;
        }

        .btn-outline-red {
            background: transparent;
            color: #ef4444;
            border: 1.5px solid #fca5a5;
        }

        .btn-outline-red:hover {
            background: #fef2f2;
        }

        /* ── MOBILE STICKY FOOTER ── */
        .mobile-footer {
            position: fixed;
            inset-x: 0;
            bottom: 0;
            z-index: 50;
            background: rgba(255, 255, 255, .95);
            backdrop-filter: blur(8px);
            border-top: 1px solid #e5e7eb;
            padding: .85rem 1rem env(safe-area-inset-bottom);
        }

        .mobile-footer-inner {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: .6rem;
        }

        .btn-mobile {
            flex: 1;
            padding: .85rem;
            border-radius: 12px;
            font-family: 'DM Sans', sans-serif;
            font-size: .9rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: all .18s;
        }

        .btn-mobile:active {
            transform: scale(.97);
        }

        .btn-mobile.yellow {
            background: #f59e0b;
            color: #fff;
            box-shadow: 0 2px 12px #f59e0b55;
        }

        .btn-mobile.green {
            background: #10b981;
            color: #fff;
            box-shadow: 0 2px 12px #10b98155;
        }

        .btn-mobile.outline-red {
            background: transparent;
            color: #ef4444;
            border: 1.5px solid #fca5a5;
        }

        @media(min-width:640px) {
            .mobile-footer {
                display: none;
            }
        }

        @media(max-width:639px) {
            .form-actions {
                display: none;
            }

            .mov-root {
                padding-bottom: 8rem;
            }
        }

        /* error msg */
        #errorMsg:not(:empty) {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            padding: .6rem .9rem;
            font-size: .82rem;
            color: #b91c1c;
            margin-top: .5rem;
        }

        .input-error {
            border: 1px solid #ef4444;
            background-color: #fef2f2;
        }

        /* Para manter o foco também com borda vermelha */
        .input-error:focus {
            border-color: #dc2626;
            outline: none;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }
    </style>

    <div class="mov-root">
        <div class="mov-card">

            {{-- ── HEADER ── --}}
            <div class="mov-header">
                <div class="mov-header-top">
                    <div class="mov-avatar {{ $isStarting ? 'yellow' : 'green' }}">
                        {{ $isStarting ? '🚗' : '📦' }}
                    </div>
                    <div style="flex:1">
                        <h1 class="mov-title">{{ $isStarting ? 'Saída de Veículo' : 'Entrega de Veículo' }}</h1>
                        <p class="mov-meta">
                            Motorista — <span>{{ Auth::user()->name }}</span> &nbsp;·&nbsp;
                            {{ $veiculos->first()->marca ?? '' }} {{ $veiculos->first()->modelo ?? '' }}
                        </p>
                    </div>
                    <span class="mov-badge {{ $isStarting ? 'yellow' : 'green' }}">
                        {{ $isStarting ? '🟡 Saindo' : '🟢 Entregando' }}
                    </span>
                </div>

                {{-- ── TIMELINE ── --}}
                <div class="timeline-wrap">
                    <div class="timeline">
                        <div class="timeline-track">
                            <div class="timeline-fill step{{ $step }}"></div>
                        </div>

                        {{-- Nó 1: Aguardando --}}
                        <div class="tl-node">
                            <div class="tl-dot {{ $step >= 1 ? 'done' : 'active' }}">
                                {{ $step >= 1 ? '✓' : '🏁' }}
                            </div>
                            <div class="tl-label {{ $step == 0 ? 'active' : 'done' }}">Saída</div>
                            @if (!$isStarting)
                                <div class="tl-time">{{ $movimentacao->first()->created_at?->format('H:i') ?? '' }}</div>
                            @endif
                        </div>

                        {{-- Nó 2: Em Corrida --}}
                        <div class="tl-node">
                            <div class="tl-dot {{ $step == 0 ? 'pending' : ($step == 1 ? 'active' : 'done') }}">
                                {{ $step == 0 ? '🛣️' : ($step == 1 ? '🛣️' : '✓') }}
                            </div>
                            <div class="tl-label {{ $step == 1 ? 'active' : ($step > 1 ? 'done' : '') }}">Em Corrida</div>
                            @if (!$isStarting)
                                <div class="tl-time">Agora</div>
                            @endif
                        </div>

                        {{-- Nó 3: Finalizado --}}
                        <div class="tl-node">
                            <div class="tl-dot {{ $step == 2 ? 'done' : 'pending' }}">
                                {{ $step == 2 ? '✓' : '🏆' }}
                            </div>
                            <div class="tl-label {{ $step == 2 ? 'done' : '' }}">Chegada</div>
                            @if ($step == 2)
                                <div class="tl-time">{{ now()->format('H:i') }}</div>
                            @endif
                        </div>
                    </div>

                    <p class="timeline-hint">
                        {{ $isStarting
                            ? 'Preencha os dados e clique em Iniciar Corrida para registrar a saída.'
                            : 'Informe o KM final e conclua a corrida para registrar a chegada.' }}
                    </p>
                </div>
            </div>

            {{-- ── FORM ── --}}
            <form id="movForm" method="POST"
                @if ($movimentacao->isEmpty()) action="{{ route('movimentacao.store') }}"
            @else
                action="{{ route('movimentacao.update', $movimentacao->first()->id) }}" @endif
                class="mov-form" novalidate>
                @csrf

                {{-- Campos ocultos: data, hora, status, veiculo, motorista, combustivel --}}
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
                                {{ $v->placa }} - {{ $v->modelo }}
                            </option>
                        @endforeach
                    @else
                        <option selected value="{{ $movimentacao->first()->veiculo_id }}"
                            data-combustivel="{{ $movimentacao->first()->veiculo->combustivel }}"
                            data-km="{{ $movimentacao->first()->veiculo->km_atual }}">
                        </option>
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

                {{-- ── KM GROUP ── --}}
                <div class="km-group">
                    <div class="km-group-title">Quilometragem</div>
                    <div class="form-row {{ $isStarting ? '' : 'cols2' }}">

                        {{-- KM Inicial --}}
                        <div>
                            <label class="form-label" for="km_inicial">KM Inicial <span class="req">*</span></label>
                            @if ($movimentacao->isEmpty())
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" type="number" step="0.1"
                                    value="{{ old('km_inicial') }}" placeholder="Ex: 12345.5" class="form-input" required>
                                <p class="form-hint">Leia o hodômetro antes de sair</p>
                            @else
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" type="number" step="0.1"
                                    value="{{ old('km_inicial') ?? ($movimentacao->first()->km_inicial ?? 0) }}"
                                    class="form-input readonly-look" readonly>
                            @endif
                        </div>

                        {{-- KM Final (só no modo entregando) --}}
                        @if (!$movimentacao->isEmpty())
                            <div>
                                <label class="form-label" for="km_final">KM Final <span class="req">*</span></label>
                                <input name="km_final" id="km_final" inputmode="decimal" type="number" step="0.1"
                                    placeholder="Ex: 12400" value="{{ old('km_final') ?? '' }}"
                                    class="form-input @error('km_final') input-error @enderror" required>
                            </div>
                        @else
                            <input name="km_final" id="km_final" type="number" step="0.1" disabled hidden>
                        @endif

                    </div>

                    {{-- KM Rodado (oculto mas enviado) --}}
                    <input name="km_rodado" id="km_rodado" type="hidden" value="{{ old('km_rodado', '0.0') }}">

                    {{-- Preview KM rodado (só no modo entregando) --}}
                    @if (!$movimentacao->isEmpty())
                        <div id="kmRodadoPreview"
                            style="margin-top:.5rem; font-size:.82rem; color:#6b7280; display:flex; align-items:center; gap:.4rem;">
                            <span>🛣️ KM Rodado:</span>
                            <strong id="kmRodadoVal" style="color:#111827">—</strong>
                        </div>
                    @endif
                </div>

                {{-- ── ORIGEM / DESTINO ── --}}
                <div class="form-divider"><span>Rota</span></div>

                <div class="form-row cols2">
                    <div>
                        <label class="form-label" for="origem">Origem <span class="req">*</span></label>
                        <input name="origem" id="origem" type="text"
                            value="{{ old('origem') ?? ($movimentacao->first()->origem ?? '') }}" placeholder="Ex: Sama"
                            class="form-input" required>
                        @error('origem')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label" for="destino">Destino <span class="req">*</span></label>
                        <input name="destino" id="destino" type="text"
                            value="{{ old('destino') ?? ($movimentacao->first()->destino ?? '') }}"
                            placeholder="Ex: Paço" class="form-input" required>
                        @error('destino')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ── OBSERVAÇÕES ── --}}
                <div class="form-divider"><span>Observações</span></div>
                <textarea name="observacao" id="observacoes" rows="3" class="form-input"
                    placeholder="Opcional — informe qualquer dado relevante">{{ old('observacoes') ?? ($movimentacao->first()->observacoes ?? '') }}</textarea>

                <div id="errorMsg" role="alert" aria-live="polite" style="margin-top:.5rem;"></div>

                {{-- ── DESKTOP ACTIONS ── --}}
                <div class="form-actions">
                    <span class="form-actions-hint">Campos com <span style="color:#ef4444">*</span> são
                        obrigatórios</span>
                    <div style="display:flex;gap:.6rem;align-items:center;">
                        @if (!$movimentacao->isEmpty())
                            <a href="{{ route('movimentacao.cancelar', ['id' => $movimentacao->first()->id, 'veiculoId' => $movimentacao->first()->veiculo_id]) }}"
                                class="btn btn-outline-red">Cancelar</a>
                        @endif
                        @if ($movimentacao->isEmpty())
                            <button type="submit" class="btn btn-yellow">🚗 Iniciar Corrida</button>
                        @else
                            <button type="submit" class="btn btn-green">🏁 Concluir Corrida</button>
                        @endif
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- ── MOBILE STICKY FOOTER ── --}}
    <div class="mobile-footer">
        <div class="mobile-footer-inner">
            @if (!$movimentacao->isEmpty())
                <a href="{{ route('movimentacao.cancelar', ['id' => $movimentacao->first()->id, 'veiculoId' => $movimentacao->first()->veiculo_id]) }}"
                    class="btn-mobile outline-red">Cancelar</a>
            @endif
            @if ($movimentacao->isEmpty())
                <button class="btn-mobile yellow" onclick="document.getElementById('movForm').submit()">
                    🚗 Iniciar Corrida
                </button>
            @else
                <button class="btn-mobile green" onclick="document.getElementById('movForm').submit()">
                    🏁 Concluir Corrida
                </button>
            @endif
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
            const kmRodadoVal = document.getElementById('kmRodadoVal');
            const errorMsg = document.getElementById('errorMsg');

            // ── Preenche KM e combustível a partir do veículo selecionado ──
            function applySelectedVehicle(opt) {
                if (!opt) return;
                const km = opt.getAttribute('data-km') || '';
                const combustivel = opt.getAttribute('data-combustivel') || '';
                if (kmInicialField && km !== '') kmInicialField.value = km;
                if (tipoField) tipoField.value = combustivel;
                recalc();
            }

            if (selVeiculo) {
                selVeiculo.addEventListener('change', function() {
                    applySelectedVehicle(this.options[this.selectedIndex]);
                });
                // aplica na carga inicial
                const initOpt = selVeiculo.querySelector('option[selected]') || selVeiculo.options[0];
                applySelectedVehicle(initOpt);
            }

            // ── Recalcula KM rodado ──
            function recalc() {
                if (!kmInicialField || !kmFinal || !kmRodado) return;
                const a = parseFloat(kmInicialField.value);
                const b = parseFloat(kmFinal.value);

                if (!kmFinal.value || isNaN(b)) {
                    kmRodado.value = '0.0';
                    if (kmRodadoVal) kmRodadoVal.textContent = '—';
                    if (errorMsg) errorMsg.textContent = '';
                    return;
                }
                if (isNaN(a)) {
                    if (errorMsg) errorMsg.textContent = 'Informe um KM inicial válido.';
                    return;
                }
                const diff = b - a;
                if (diff < 0) {
                    kmRodado.value = '';
                    if (kmRodadoVal) kmRodadoVal.textContent = '⚠️ inválido';
                    if (errorMsg) errorMsg.textContent = 'KM Final não pode ser menor que KM Inicial.';
                } else {
                    kmRodado.value = diff.toFixed(1);
                    if (kmRodadoVal) kmRodadoVal.textContent = diff.toFixed(1) + ' km';
                    if (errorMsg) errorMsg.textContent = '';
                }
            }

            if (kmInicialField) kmInicialField.addEventListener('input', recalc);
            if (kmFinal) kmFinal.addEventListener('input', recalc);
            recalc();
        });
    </script>
@endpush
