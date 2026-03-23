@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
        <div id="pageContainer" class="max-w-xl mx-auto pb-24">
            <!-- pb-24 para evitar conteúdo escondido pelo footer móvel -->
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                @php
                    // determina o modo com base em movimentacao: se não tem -> saindo (iniciar), senão -> entregando (finalizar)
                    $isStarting = $movimentacao->isEmpty();
                    $mode = $isStarting ? 'saindo' : 'entregando'; // saindo | entregando
                    // badge classes
                    $badgeClasses = $isStarting ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                    $badgeLabel = $isStarting ? 'Saindo — iniciar' : 'Entregando — finalizar';
                    // header bg
                    $headerBg = $isStarting ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200';
                @endphp



                <!-- VISUAL HEADER: muda conforme modo -->
                <div id="topHeader" class="px-6 py-5 border-b {{ $headerBg }}">
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center rounded-full w-12 h-12 {{ $isStarting ? 'bg-yellow-200' : 'bg-green-200' }}">
                                <!-- emoji simples para reforçar visual; troque por SVG se quiser -->
                                <span class="text-xl" aria-hidden="true">{{ $isStarting ? '🚗' : '📦' }}</span>
                            </div>
                            <div>
                                <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">
                                    {{ $isStarting ? 'Saída de Veículo' : 'Entrega de Veículo' }}
                                </h1>
                                <p class="text-sm text-gray-500 mt-1">Motorista — <span
                                        class="text-blue-600">{{ Auth::user()->name }}</span></p>
                            </div>
                        </div>

                        <!-- status badge visível -->
                        <div>
                            <span id="statusBadge"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $badgeClasses }}"
                                role="status" aria-live="polite" aria-atomic="true">
                                {{ $badgeLabel }}
                            </span>
                        </div>
                    </div>
                    <p id="modeHint" class="text-xs mt-2 text-gray-600">
                        {{ $isStarting ? 'Preencha o KM inicial e os dados de origem/destino. Clique em "Iniciar Corrida" quando sair.' : 'Informe o KM final e finalize a corrida para registrar a entrega.' }}
                    </p>
                </div>
                <form id="movForm" method="POST"
                    @if ($movimentacao->isEmpty()) action="{{ route('movimentacao.store') }}" @else action="{{ route('movimentacao.update', $movimentacao->first()->id) }}" @endif
                    class="px-6 py-6 space-y-6" novalidate>
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" hidden>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700" hidden>Data</span>
                            @if ($movimentacao->isEmpty())
                                <input name="data" id="data" type="date"
                                    value="{{ old('data', date('Y-m-d')) }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" hidden>
                            @else
                                <input name="data_fim" id="data" type="date"
                                    value="{{ old('data_fim', date('Y-m-d')) }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" hidden>
                            @endif
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700" hidden>Hora</span>
                            @if ($movimentacao->isEmpty())
                                <input name="hora" id="hora" type="time" value="{{ old('hora', date('H:i')) }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" hidden>
                            @else
                                <input name="hora_fim" id="hora" type="time"
                                    value="{{ old('hora_fim', date('H:i')) }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    required aria-required="true" hidden>
                            @endif
                        </label>
                    </div>
                    @php
                        // manter status enviado ao backend (ativa / finalizada / cancelada)
                        if ($movimentacao->isEmpty()) {
                            $currentStatus = old('status', 'ativa');
                        } else {
                            $currentStatus = 'finalizada';
                        }
                    @endphp


                    <!-- hidden status input (enviado) -->
                    <input type="hidden" name="status" id="status" value="{{ $currentStatus }}">

                    <!-- veiculo + motorista (mantive sua lógica mas escondendo labels se necessário) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" hidden>
                        <label class="block" hidden>
                            <select name="veiculo_id" id="veiculo_id" hidden
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
                        </label>

                        <label class="block" hidden>
                            <select name="user_id" id="motorista_id" hidden
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true">
                                @if ($movimentacao->isEmpty())
                                    <option selected value="{{ $user->id }}">{{ $user->name }}</option>
                                @else
                                    <option selected value="{{ $movimentacao->first()->user->id }}">
                                        {{ $movimentacao->first()->user->name }}</option>
                                @endif
                            </select>
                        </label>
                    </div>

                    <!-- combustivel -->
                    <div hidden>
                        <label class="block">
                            <input id="tipo_combustivel" name="tipo_combustivel" type="text"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3"
                                value="{{ old('tipo_combustivel') ?? ($movimentacao->first()->tipo_combustivel ?? '') }}">
                        </label>
                    </div>

                    <!-- ROW: KM (aqui mudamos visual e comportamento) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">KM Inicial <span
                                    class="text-red-500">*</span></span>
                            @if ($movimentacao->isEmpty())
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" type="number" step="0.1"
                                    value="{{ old('km_inicial') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none"
                                    required aria-required="true" aria-describedby="kmInitHelp">
                                <small id="kmInitHelp" class="text-xs text-gray-400">Ex: 12345.5</small>
                            @else
                                <input name="km_inicial" id="km_inicial" inputmode="decimal" type="number" step="0.1"
                                    value="{{ old('km_inicial') ?? ($movimentacao->first()->km_inicial ?? 0) }}"
                                    class="mt-1 w-full rounded-lg border-gray-200 bg-gray-100 shadow-sm py-3 px-3" readonly
                                    aria-readonly="true">
                            @endif
                        </label>

                        <label class="block">
                            @if ($movimentacao->isEmpty())
                                {{-- ao iniciar, KM final começa desabilitado/oculto para indicar que ainda não foi entregue --}}
                                <input name="km_final" id="km_final" inputmode="decimal" type="number" step="0.1"
                                    disabled hidden class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3">
                            @else
                                <span class="text-sm font-medium text-gray-700">KM Final <span
                                        class="text-red-500">*</span></span>
                                <input name="km_final" id="km_final" inputmode="decimal" type="number" step="0.1"
                                    value="{{ old('km_final') ?? $movimentacao->first()->km_inicial }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3" required
                                    aria-required="true">
                            @endif
                        </label>

                        <label class="" hidden>
                            <span class="text-sm font-medium text-gray-700">KM Rodado <span
                                    class="text-red-500">*</span></span>
                            <input name="km_rodado" id="km_rodado" type="number"
                                value="{{ old('km_rodado') ?? '0.0' }}"
                                class="mt-1 w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm py-3 px-3" readonly
                                aria-readonly="true">
                        </label>
                    </div>

                    <!-- origem / destino -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Origem<span
                                    class="text-red-500">*</span></span>
                            <input name="origem" id="origem" type="text" value="{{ old('origem') ?? 'SAMA' }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3" required
                                aria-required="true">
                            @error('origem')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Destino <span
                                    class="text-red-500">*</span></span>
                            <input name="destino" id="destino" type="text"
                                value="{{ old('destino') ?? ($movimentacao->first()->destino ?? '') }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3" required
                                aria-required="true">
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
                            <textarea name="observacao" id="observacoes" rows="3"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3" placeholder="Opcional">{{ old('observacoes') ?? ($movimentacao->first()->observacoes ?? '') }}</textarea>
                        </label>
                    </div>

                    <div id="errorMsg" class="text-sm text-red-600" role="alert" aria-live="polite"></div>

                    <!-- desktop action row -->
                    <div class="sm:flex mobile-hidden items-center justify-between mt-2">
                        <div class="text-sm text-gray-500">Campos com * são obrigatórios</div>
                        <div class="flex gap-3">
                            @if ($movimentacao->isEmpty())
                                <button id="submitBtn" type="submit"
                                    class="px-4 py-2 rounded-lg bg-yellow-600 text-white shadow-sm">Iniciar
                                    Corrida</button>
                            @else
                                <a href="{{ route('movimentacao.cancelar', [
                                    'id' => $movimentacao->first()->id,
                                    'veiculoId' => $movimentacao->first()->veiculo_id,
                                ]) }}"
                                    class="px-4 py-2 rounded-lg border border-red-300 text-red-700">Cancelar</a>
                                <button id="submitBtn" type="submit"
                                    class="px-4 py-2 rounded-lg bg-green-600 text-white shadow-sm">Concluir
                                    Corrida</button>
                            @endif
                        </div>
                    </div>
                </form>

                <!-- Mobile fixed buttons (visible only on small screens) -->
                <div class="sm:hidden fixed inset-x-0 bottom-0 z-50 bg-white border-t p-3">
                    <div class="max-w-xl mx-auto flex gap-3">
                        <a href="@if ($movimentacao->isEmpty()) {{ url()->previous() }} @else {{ route('movimentacao.cancelar', [
                            'id' => $movimentacao->first()->id,
                            'veiculoId' => $movimentacao->first()->veiculo_id,
                        ]) }} @endif"
                            class="flex-1 text-center py-3 rounded-lg border border-gray-300 text-sm">
                            Cancelar
                        </a>

                        @if ($movimentacao->isEmpty())
                            <button id="mobilePrimary" type="button"
                                onclick="document.getElementById('movForm').submit()"
                                class="flex-1 py-3 rounded-lg bg-yellow-600 text-white text-sm">
                                Iniciar Corrida
                            </button>
                        @else
                            <button id="mobilePrimary" type="button"
                                onclick="document.getElementById('movForm').submit()"
                                class="flex-1 py-3 rounded-lg bg-green-600 text-white text-sm">
                                Concluir Corrida
                            </button>
                        @endif
                    </div>
                </div>
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
            const form = document.getElementById('movForm');

            // elementos visuais
            const statusBadge = document.getElementById('statusBadge');
            const topHeader = document.getElementById('topHeader');
            const modeHint = document.getElementById('modeHint');
            const mobilePrimary = document.getElementById('mobilePrimary');
            const submitBtn = document.getElementById('submitBtn');

            // Modo inicial passado do servidor via blade (saindo | entregando)
            const initialMode = "{{ $mode }}";

            // --- Marca permanentemente os inputs visíveis e editáveis com borda vermelha ---
            (function markVisibleFieldsRed() {
                if (!form) return;
                const controls = form.querySelectorAll('input:not([type=hidden]), select, textarea');
                controls.forEach(el => {
                    // ignora elementos invisíveis
                    if (el.hasAttribute('hidden')) return;
                    const cs = window.getComputedStyle(el);
                    if (cs.display === 'none' || cs.visibility === 'hidden') return;
                    if (el.readOnly || el.disabled) return;

                    // remove classes de borda cinza comuns para evitar conflito
                    el.classList.remove('border-gray-300', 'border-gray-200', 'bg-gray-50');

                    // adiciona classes Tailwind para borda vermelha (persistente)
                    el.classList.add('border-2', 'border-blue-600', 'focus:ring-blue-400',
                        'focus:border-blue-600');

                    // fallback inline
                    el.style.borderColor = '#add8e6';
                    el.style.borderWidth = '2px';
                    el.style.boxShadow = 'none';
                });
            })();

            if (!selVeiculo) {
                // se não houver select, podemos ainda controlar modo
            } else {
                // atualiza km/tipo a partir do option selecionado
                function applySelectedVehicle(option) {
                    if (!option) return;
                    const km = option.getAttribute('data-km') || '';
                    const combustivel = option.getAttribute('data-combustivel') || '';
                    if (kmInicialField && (km !== '')) kmInicialField.value = km;
                    if (tipoField) tipoField.value = combustivel;
                    recalcKmRodado();
                }

                selVeiculo.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    applySelectedVehicle(opt);
                });

                (function applyInitialSelection() {
                    let initialOpt = null;
                    if (selVeiculo.selectedIndex >= 0) initialOpt = selVeiculo.options[selVeiculo
                        .selectedIndex];
                    if ((!initialOpt || initialOpt.value === '') && selVeiculo.querySelector(
                            'option[selected]')) {
                        initialOpt = selVeiculo.querySelector('option[selected]');
                    }
                    if ((!initialOpt || initialOpt.value === '')) {
                        const withDataKm = selVeiculo.querySelector('option[data-km]');
                        if (withDataKm && withDataKm.value !== '') initialOpt = withDataKm;
                    }
                    applySelectedVehicle(initialOpt);
                })();
            }

            // --- KM rodado ---
            function recalcKmRodado() {
                if (!kmInicialField || !kmFinal || !kmRodado) return;

                const a = parseFloat(kmInicialField.value === '' ? NaN : kmInicialField.value);
                const b = parseFloat(kmFinal.value === '' ? NaN : kmFinal.value);

                if (kmFinal.value === '' || isNaN(b)) {
                    kmRodado.value = '0.0';
                    if (errorMsg) errorMsg.textContent = '';
                    kmFinal && kmFinal.removeAttribute('aria-invalid');
                    return;
                }

                if (isNaN(a)) {
                    kmRodado.value = '';
                    if (errorMsg) errorMsg.textContent = 'Informe um KM inicial válido.';
                    return;
                }

                const diff = b - a;
                if (diff < 0) {
                    kmRodado.value = '';
                    if (errorMsg) errorMsg.textContent = 'KM Final não pode ser menor que KM Inicial.';
                    kmFinal && kmFinal.setAttribute('aria-invalid', 'true');
                } else {
                    kmRodado.value = diff.toFixed(1);
                    if (errorMsg) errorMsg.textContent = '';
                    kmFinal && kmFinal.removeAttribute('aria-invalid');
                }
            }

            if (kmInicialField) kmInicialField.addEventListener('input', recalcKmRodado);
            if (kmFinal) kmFinal.addEventListener('input', recalcKmRodado);

            // --- MODO UI (saindo | entregando) ---
            function setMode(mode) {
                // mode: 'saindo' | 'entregando'
                if (!statusBadge || !topHeader) return;

                if (mode === 'saindo') {
                    // visual amarelo
                    statusBadge.textContent = 'Saindo — iniciar';
                    statusBadge.className =
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';

                    topHeader.classList.remove('bg-green-50', 'border-green-200');
                    topHeader.classList.add('bg-yellow-50', 'border-yellow-200');

                    // hint
                    modeHint.textContent =
                        'Preencha o KM inicial e os dados de origem/destino. Clique em "Iniciar Corrida" quando sair.';

                    // KM fields: permitir km_inicial, desabilitar km_final
                    if (kmInicialField) {
                        kmInicialField.removeAttribute('readonly');
                        kmInicialField.classList.remove('bg-gray-100');
                        kmInicialField.classList.add('border-2', 'border-red-600');
                    }
                    if (kmFinal) {
                        kmFinal.disabled = true;
                        kmFinal.setAttribute('hidden', 'true');
                    }

                    // botões
                    if (submitBtn) submitBtn.textContent = 'Iniciar Corrida';
                    if (mobilePrimary) {
                        mobilePrimary.textContent = 'Iniciar Corrida';
                        mobilePrimary.className = 'flex-1 py-3 rounded-lg bg-yellow-600 text-white text-sm';
                    }
                } else {
                    // entregando - visual verde
                    statusBadge.textContent = 'Entregando — finalizar';
                    statusBadge.className =
                        'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';

                    topHeader.classList.remove('bg-yellow-50', 'border-yellow-200');
                    topHeader.classList.add('bg-green-50', 'border-green-200');

                    modeHint.textContent = 'Informe o KM final e finalize a corrida para registrar a entrega.';

                    // KM: km_inicial readonly, km_final enabled
                    if (kmInicialField) {
                        kmInicialField.setAttribute('readonly', 'true');
                        kmInicialField.classList.add('bg-gray-100');
                    }
                    if (kmFinal) {
                        kmFinal.disabled = false;
                        kmFinal.removeAttribute('hidden');
                        kmFinal.classList.remove('hidden');
                    }

                    if (submitBtn) submitBtn.textContent = 'Concluir Corrida';
                    if (mobilePrimary) {
                        mobilePrimary.textContent = 'Concluir Corrida';
                        mobilePrimary.className = 'flex-1 py-3 rounded-lg bg-green-600 text-white text-sm';
                    }
                }
            }

            // inicializa UI com modo do servidor
            setMode(initialMode);

            // recálculo imediato caso já esteja em modo 'entregando'
            recalcKmRodado();

            // --- scroll para o meio em mobile ---
            (function() {
                let scrolledToMiddle = false;

                function scrollToMiddleOnMobile() {
                    // considera mobile qualquer largura menor que 768px (ajuste se quiser)
                    if (window.innerWidth >= 768) return;

                    const el = document.getElementById('pageContainer');
                    if (!el) return;

                    // calcula posição absoluta do elemento
                    const rect = el.getBoundingClientRect();
                    const absoluteTop = window.scrollY + rect.top;

                    // posição desejada: centro do elemento alinhado ao centro da viewport
                    const target = absoluteTop - (window.innerHeight / 2) + (rect.height / 2);

                    // não tentar scroll negativo
                    const final = Math.max(0, Math.round(target));

                    // anima o scroll suavemente
                    window.scrollTo({
                        top: final,
                        behavior: 'smooth'
                    });

                    scrolledToMiddle = true;
                }

                // espera um pouquinho pro layout estabilizar (fonts, imagens etc.)
                setTimeout(function() {
                    if (!scrolledToMiddle) scrollToMiddleOnMobile();
                }, 200);

                // reaplica se rotacionar o aparelho (útil quando o usuário abre e gira a tela)
                window.addEventListener('orientationchange', function() {
                    // espera o browser reajustar
                    setTimeout(scrollToMiddleOnMobile, 300);
                });

                // se o usuário redimensionar (ex: teclado fecha/abre), tenta novamente uma vez
                let resizeTimer = null;
                window.addEventListener('resize', function() {
                    if (resizeTimer) clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function() {
                        if (!scrolledToMiddle && window.innerWidth < 768)
                            scrollToMiddleOnMobile();
                    }, 250);
                });
            })();
        });
    </script>
@endpush
