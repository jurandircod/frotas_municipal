{{-- resources/views/movimentacoes/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Movimentações')
@section('page_header', 'Movimentações')
@section('page_actions')
    <a href="{{ route('movimentacao.index') }}"
        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
        Nova Movimentação
    </a>
@endsection

@section('content')
    <div class="space-y-4">
        <div id="movementsCard" class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Movimentações</h3>
                    <p class="text-xs text-gray-500">Visualize, edite ou exclua movimentações.</p>
                </div>

                {{-- Campo de busca --}}
                <div class="ml-4 w-full max-w-sm">
                    <label for="searchMov" class="sr-only">Pesquisar movimentação</label>
                    <div class="relative">
                        <input id="searchMov" type="search" placeholder="Pesquisar por placa, motorista, origem, destino ou data"
                            class="w-full rounded-lg border-gray-300 shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm">
                        <button id="clearSearch" type="button" class="absolute right-1 top-1/2 -translate-y-1/2 text-gray-500 text-sm hidden">✕</button>
                    </div>
                    <div id="searchCount" class="text-xs text-gray-500 mt-1">Mostrando <span id="searchCountNumber">{{ $movimentacoes->count() }}</span> de {{ $movimentacoes->total() ?? $movimentacoes->count() }}</div>
                </div>
            </div>

            {{-- Table (desktop) --}}
            <div class="mt-4 hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" id="movTable">
                        <thead class="text-xs text-gray-500 text-left">
                            <tr>
                                <th class="px-3 py-2">Data / Hora Inicio</th>
                                <th class="px-3 py-2">Data / Hora Fim</th>
                                <th class="px-3 py-2">Motorista</th>
                                <th class="px-3 py-2">Veículo</th>
                                <th class="px-3 py-2">Origem → Destino</th>
                                <th class="px-3 py-2">KM Rodado</th>
                                <th class="px-3 py-2 w-48">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movimentacoes as $m)
                                <tr class="border-t">
                                    <td class="px-3 py-3 text-gray-800">
                                        {{ \Carbon\Carbon::parse($m->data)->format('d/m/Y') }} {{ substr($m->hora ?? '', 0, 5) }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($m->data_fim)->format('d/m/Y') }} {{ substr($m->hora_fim ?? '', 0, 5) }}
                                    </td>
                                    <td class="px-3 py-3">{{ $m->user->name ?? ($m->motorista_nome ?? '-') }}</td>
                                    <td class="px-3 py-3">{{ $m->veiculo_placa ?? ($m->veiculo->placa ?? '-') }}
                                        @if ($m->veiculo and $m->veiculo->modelo)
                                            • {{ $m->veiculo->modelo }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $m->origem }} → {{ $m->destino }}</td>
                                    <td class="px-3 py-3">
                                        {{ number_format($m->km_rodado ?? ($m->km_final - $m->km_inicial ?? 0), 1, ',', '.') }}
                                        km</td>
                                    <td class="px-3 py-3">
                                        <div class="flex gap-2">
                                            <button type="button"
                                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                                data-id="{{ $m->id }}"
                                                data-data="{{ optional($m->data)->format('Y-m-d') ?? '' }}"
                                                data-hora="{{ $m->hora ?? '' }}"
                                                data-data_fim="{{ optional($m->data_fim)->format('Y-m-d') ?? '' }}"
                                                data-hora_fim="{{ $m->hora_fim ?? '' }}"
                                                data-veiculo="{{ $m->veiculo_id ?? ($m->veiculo->id ?? '') }}"
                                                data-motorista="{{ $m->user->id ?? ($m->user->id ?? '') }}"
                                                data-tipo="{{ $m->tipo_combustivel ?? '' }}"
                                                data-km_inicial="{{ $m->km_inicial ?? '' }}"
                                                data-km_final="{{ $m->km_final ?? '' }}"
                                                data-km_rodado="{{ $m->km_rodado ?? '' }}"
                                                data-origem="{{ $m->origem ?? '' }}"
                                                data-destino="{{ $m->destino ?? '' }}"
                                                data-observacoes="{{ addslashes($m->observacao ?? ($m->observacoes ?? '')) }}"
                                                data-status="{{ $m->status ?? 'ativa' }}"
                                                data-update-route="{{ route('movimentacao.update', $m->id) }}">Alterar</button>

                                            <form action="{{ route('movimentacao.destroy', $m->id) }}" method="POST"
                                                onsubmit="return confirmDelete(event, this, '{{ addslashes($m->id . ' - ' . ($m->veiculo->placa ?? '-')) }}')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-4 text-gray-500" colspan="6">Nenhuma movimentação registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile cards --}}
            <div class="mt-4 md:hidden space-y-3" id="movMobileList">
                @forelse($movimentacoes as $m)
                    <div class="border rounded-xl p-3 mov-mobile-card">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-800">
                                    {{ $m->user->name ?? ($m->motorista_nome ?? '-') }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $m->veiculo_placa ?? ($m->veiculo->placa ?? '-') }} •
                                    {{ \Carbon\Carbon::parse($m->data)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1">KM:
                                    {{ number_format($m->km_rodado ?? ($m->km_final - $m->km_inicial ?? 0), 1, ',', '.') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $m->origem }} → {{ $m->destino }}</div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <button type="button"
                                    class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm"
                                    data-id="{{ $m->id }}"
                                    data-data="{{ optional($m->data)->format('Y-m-d') ?? '' }}"
                                    data-hora="{{ $m->hora ?? '' }}"
                                    data-veiculo="{{ $m->veiculo_id ?? ($m->veiculo->id ?? '') }}"
                                    data-motorista="{{ $m->user ?? ($m->user->id ?? '') }}"
                                    data-tipo="{{ $m->tipo_combustivel ?? '' }}"
                                    data-km_inicial="{{ $m->km_inicial ?? '' }}" data-km_final="{{ $m->km_final ?? '' }}"
                                    data-km_rodado="{{ $m->km_rodado ?? '' }}" data-origem="{{ $m->origem ?? '' }}"
                                    data-destino="{{ $m->destino ?? '' }}"
                                    data-observacoes="{{ addslashes($m->observacao ?? ($m->observacoes ?? '')) }}"
                                    data-status="{{ $m->status ?? 'ativa' }}"
                                    data-update-route="{{ route('movimentacao.update', $m->id) }}">Alterar</button>

                                <form action="{{ route('movimentacao.destroy', $m->id) }}" method="POST"
                                    onsubmit="return confirmDelete(event, this, '{{ addslashes($m->id . ' - ' . ($m->veiculo->placa ?? '-')) }}')">
                                    @csrf
                                    <button type="submit"
                                        class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm">Excluir</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">Nenhuma movimentação registrada.</div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if (method_exists($movimentacoes, 'links'))
                <div class="mt-4">
                    {{ $movimentacoes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- EDIT MOVIMENTAÇÃO MODAL (mantive igual ao seu) -->
    <div id="editMovementModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black opacity-40" data-close-modal></div>

        <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Editar Movimentação</h3>
                <button type="button" class="text-gray-500" data-close-modal aria-label="Fechar">✕</button>
            </div>

            <form id="editMovementForm" method="POST" class="px-6 py-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-sm text-gray-700">Data</span>
                        <input type="date" name="data" id="edit_data"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>

                    <label class="block">
                        <span class="text-sm text-gray-700">Hora</span>
                        <input type="time" name="hora" id="edit_hora"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="block">
                        <span class="text-sm text-gray-700">Veículo</span>
                        <select name="veiculo_id" id="edit_veiculo"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                            <option value="">-- selecione --</option>
                            @foreach ($veiculos as $v)
                                <option value="{{ $v->id }}" data-km="{{ $v->km_atual }}"
                                    data-combustivel="{{ $v->combustivel }}">{{ $v->placa }} - {{ $v->modelo }}
                                </option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="text-sm text-gray-700">Motorista</span>
                        <select name="user_id" id="edit_motorista"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                            <option value="">-- selecione --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div>
                    <label class="block">
                        <span class="text-sm text-gray-700">Tipo de Combustível</span>
                        <input type="text" name="tipo_combustivel" id="edit_tipo_combustivel"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-2">
                    <label class="block">
                        <span class="text-sm text-gray-700">KM Inicial</span>
                        <input type="number" step="0.1" name="km_inicial" id="edit_km_inicial"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>

                    <label class="block">
                        <span class="text-sm text-gray-700">KM Final</span>
                        <input type="number" step="0.1" name="km_final" id="edit_km_final"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>

                    <label class="block">
                        <span class="text-sm text-gray-700">KM Rodado</span>
                        <input type="text" name="km_rodado" id="edit_km_rodado" readonly
                            class="mt-2 w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm py-2 px-3"
                            aria-readonly="true">
                    </label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                    <label class="block">
                        <span class="text-sm text-gray-700">Origem</span>
                        <input type="text" name="origem" id="edit_origem"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>
                    <label class="block">
                        <span class="text-sm text-gray-700">Destino</span>
                        <input type="text" name="destino" id="edit_destino"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3">
                    </label>
                </div>

                <div class="mt-2">
                    <label class="block">
                        <span class="text-sm text-gray-700">Observações</span>
                        <textarea name="observacoes" id="edit_observacoes" rows="3"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-2 px-3"></textarea>
                    </label>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-lg border" data-close-modal>Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ==========================
        // Busca / filtro (desktop + mobile)
        // ==========================
        (function () {
            const input = document.getElementById('searchMov');
            const clearBtn = document.getElementById('clearSearch');
            const card = document.getElementById('movementsCard');
            if (!input || !card) return;

            const tableRows = Array.from(card.querySelectorAll('table tbody tr'));
            const mobileCards = Array.from(card.querySelectorAll('.mov-mobile-card'));
            const countEl = document.getElementById('searchCountNumber');

            // normaliza (remove acentos, minuscula)
            const normalize = (str) => {
                if (!str) return '';
                try {
                    return String(str).toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
                } catch (e) {
                    // fallback limitado se normalize/unicode not supported
                    return String(str).toLowerCase()
                        .replace(/[áàâãä]/g, 'a')
                        .replace(/[éèêë]/g, 'e')
                        .replace(/[íìîï]/g, 'i')
                        .replace(/[óòôõö]/g, 'o')
                        .replace(/[úùûü]/g, 'u')
                        .replace(/[ç]/g, 'c');
                }
            };

            function filterList() {
                const q = normalize(input.value.trim());
                clearBtn.classList.toggle('hidden', q.length === 0);

                let visible = 0;

                // filtra tabela desktop
                tableRows.forEach(row => {
                    const text = normalize(row.textContent || '');
                    const show = q === '' || text.includes(q);
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });

                // filtra cards mobile
                mobileCards.forEach(cardEl => {
                    const text = normalize(cardEl.textContent || '');
                    const show = q === '' || text.includes(q);
                    cardEl.style.display = show ? '' : 'none';
                    // do not double count: if desktop is hidden (small screens) this won't matter; just count mobile separately
                    if (show && tableRows.length === 0) visible++;
                });

                // update counter: if using pagination total may differ; we show current shown count
                if (countEl) countEl.textContent = visible;
            }

            // debounce
            let timer = null;
            input.addEventListener('input', function () {
                if (timer) clearTimeout(timer);
                timer = setTimeout(filterList, 200);
            });

            clearBtn.addEventListener('click', function () {
                input.value = '';
                filterList();
                input.focus();
            });
        })();

        // ==========================
        // confirmação antes de excluir
        // ==========================
        function confirmDelete(event, form, info) {
            event.preventDefault();
            const ok = confirm('Deseja realmente excluir a movimentação (' + info + ')? Esta ação não pode ser desfeita.');
            if (ok) form.submit();
            return false;
        }

        // ==========================
        // Modal de edição (mantive seu código)
        // ==========================
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('editMovementModal');
            const form = document.getElementById('editMovementForm');
            const openButtons = document.querySelectorAll('.btn-open-edit');
            const closeTriggers = document.querySelectorAll('[data-close-modal]');

            // fields
            const fData = document.getElementById('edit_data');
            const fHora = document.getElementById('edit_hora');
            const fVeiculo = document.getElementById('edit_veiculo');
            const fMotorista = document.getElementById('edit_motorista');
            const fTipo = document.getElementById('edit_tipo_combustivel');
            const fKmIni = document.getElementById('edit_km_inicial');
            const fKmFinal = document.getElementById('edit_km_final');
            const fKmRod = document.getElementById('edit_km_rodado');
            const fOrigem = document.getElementById('edit_origem');
            const fDestino = document.getElementById('edit_destino');
            const fObs = document.getElementById('edit_observacoes');

            // recalcula km rodado
            function recalcEditKm() {
                const a = parseFloat(fKmIni.value === '' ? NaN : fKmIni.value);
                const b = parseFloat(fKmFinal.value === '' ? NaN : fKmFinal.value);
                if (isNaN(a) || isNaN(b)) {
                    fKmRod.value = '';
                    return;
                }
                const diff = b - a;
                if (diff < 0) {
                    fKmRod.value = '';
                } else {
                    fKmRod.value = diff.toFixed(1);
                }
            }

            // quando trocar veículo no modal, atualiza km inicial e tipo
            if (fVeiculo) {
                fVeiculo.addEventListener('change', function () {
                    const opt = this.options[this.selectedIndex];
                    if (!opt) return;
                    const km = opt.getAttribute('data-km') || '';
                    const tipo = opt.getAttribute('data-combustivel') || '';
                    if (km !== '') fKmIni.value = km;
                    if (tipo !== '') fTipo.value = tipo;
                    recalcEditKm();
                });
            }

            // bind inputs for recalculation
            [fKmIni, fKmFinal].forEach(el => {
                if (el) el.addEventListener('input', recalcEditKm);
            });

            // abrir modal e preencher com data-attributes
            openButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const updateRoute = this.getAttribute('data-update-route') || '';
                    // fill fields
                    if (fData) fData.value = this.getAttribute('data-data') || '';
                    if (fHora) fHora.value = this.getAttribute('data-hora') || '';
                    const vid = this.getAttribute('data-veiculo') || '';
                    if (fVeiculo) fVeiculo.value = vid;
                    if (fTipo) fTipo.value = this.getAttribute('data-tipo') || '';
                    if (fKmIni) fKmIni.value = this.getAttribute('data-km_inicial') || '';
                    if (fKmFinal) fKmFinal.value = this.getAttribute('data-km_final') || '';
                    if (fKmRod) fKmRod.value = this.getAttribute('data-km_rodado') || '';
                    if (fMotorista) fMotorista.value = this.getAttribute('data-motorista') || '';
                    if (fOrigem) fOrigem.value = this.getAttribute('data-origem') || '';
                    if (fDestino) fDestino.value = this.getAttribute('data-destino') || '';
                    if (fObs) fObs.value = this.getAttribute('data-observacoes') || '';
                    

                    // set form action
                    if (form) form.action = updateRoute;

                    // show modal
                    if (modal) {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        setTimeout(() => { if (fData) fData.focus(); }, 150);
                    }
                });
            });

            // close handlers
            closeTriggers.forEach(t => t.addEventListener('click', closeModal));
            if (modal) modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
            });

            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            // form validation before submit
            if (form) {
                form.addEventListener('submit', function (e) {
                    // basic checks: data, motorista, veiculo, km values
                    if (fData && !fData.value) { e.preventDefault(); alert('Informe a data.'); fData.focus(); return; }
                    if (fMotorista && !fMotorista.value) { e.preventDefault(); alert('Informe o motorista.'); fMotorista.focus(); return; }
                    if (fVeiculo && !fVeiculo.value) { e.preventDefault(); alert('Informe o veículo.'); fVeiculo.focus(); return; }

                    const a = parseFloat(fKmIni.value || NaN);
                    const b = parseFloat(fKmFinal.value || NaN);

                    if (isNaN(a) || isNaN(b)) { e.preventDefault(); alert('Informe KM inicial e final válidos.'); return; }
                    if (b < a) { e.preventDefault(); alert('KM Final não pode ser menor que KM Inicial.'); fKmFinal.focus(); return; }
                    // if ok, the form will submit (PUT) to update route
                });
            }
        });
    </script>
@endpush