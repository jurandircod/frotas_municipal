{{-- resources/views/cartoes/lista.blade.php --}}
@extends('layouts.app')

@section('title', 'Cartões')
@section('page_header', 'Cartões')

@section('page_actions')
    <button type="button" id="btnOpenCreate"
        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
        Cadastrar Cartão
    </button>
@endsection

@section('content')
    <div class="space-y-4">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('cartao.list') }}" class="bg-gray-50 border rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome do veículo</label>
                    <input type="text" name="nome_veiculo" value="{{ request('nome_veiculo') }}"
                        placeholder="Buscar por veículo"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Placa</label>
                    <input type="text" name="placa" value="{{ request('placa') }}" placeholder="Buscar por placa"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Número do cartão</label>
                    <input type="text" name="numero_cartao" value="{{ request('numero_cartao') }}"
                        placeholder="Buscar por número"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm hover:bg-blue-700">
                        Buscar
                    </button>

                    <a href="{{ route('cartao.list') }}"
                        class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                        Limpar
                    </a>
                </div>
            </div>
        </form>

        {{-- Table (desktop) --}}
        <div class="hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 text-left">
                        <tr>
                            <th class="px-3 py-2">Nome do veículo</th>
                            <th class="px-3 py-2">Placa</th>
                            <th class="px-3 py-2">Número do cartão</th>
                            <th class="px-3 py-2">Horímetro</th>
                            <th class="px-3 py-2">Aumento</th>
                            <th class="px-3 py-2 w-48">Ações</th>
                            <th class="px-3 py-2">QR Retirada</th>
                            <th class="px-3 py-2">QR Entrega</th>
                            <th class="px-3 py-2">Gerar qrcodes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cartoes as $c)
                            <tr class="border-t">
                                <td class="px-3 py-3 text-gray-800 font-medium">{{ $c->nome_veiculo }}</td>
                                <td class="px-3 py-3">{{ $c->placa }}</td>
                                <td class="px-3 py-3">{{ $c->numero_cartao }}</td>
                                <td class="px-3 py-3">{{ $c->horimetro ?? '-' }}</td>
                                <td class="px-3 py-3">{{ $c->aumento_horimetro ?? '-' }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                            data-id="{{ $c->id }}" data-nome-veiculo="{{ $c->nome_veiculo }}"
                                            data-placa="{{ $c->placa }}" data-numero-cartao="{{ $c->numero_cartao }}"
                                            data-horimetro="{{ $c->horimetro }}"
                                            data-aumento-horimetro="{{ $c->aumento_horimetro }}"
                                            data-update-route="{{ route('cartao.update', $c->id) }}">
                                            Alterar
                                        </button>

                                        <form action="{{ route('cartao.destroy', $c->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event, this, '{{ addslashes($c->nome_veiculo) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-3 py-3">
                                    @if ($c->qr_retirada_url)
                                        <a href="{{ $c->qr_retirada_url }}" target="_blank">
                                            <img src="{{ $c->qr_retirada_url }}" alt="QR Retirada" class="w-20 h-20">
                                        </a>
                                    @else
                                        <span class="text-gray-400">Sem QR</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3">
                                    @if ($c->qr_entrega_url)
                                        <a href="{{ $c->qr_entrega_url }}" target="_blank">
                                            <img src="{{ $c->qr_entrega_url }}" alt="QR Entrega" class="w-20 h-20">
                                        </a>
                                    @else
                                        <span class="text-gray-400">Sem QR</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3">
                                    <div class="flex flex-col gap-2">
                                        <form action="{{ route('cartao.qrcode.retirada.gerar', $c->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 rounded-md bg-blue-50 text-blue-700 text-sm border">
                                                Gerar QR Retirada
                                            </button>
                                        </form>

                                        <form action="{{ route('cartao.qrcode.entrega.gerar', $c->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-3 py-1 rounded-md bg-green-50 text-green-700 text-sm border">
                                                Gerar QR Entrega
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-4 text-gray-500" colspan="6">Nenhum cartão cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden space-y-3">
            @forelse($cartoes as $c)
                <div class="border rounded-xl p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-medium text-gray-800">{{ $c->nome_veiculo }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Placa: {{ $c->placa }} • Cartão: {{ $c->numero_cartao }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                Horímetro: {{ $c->horimetro ?? '-' }} • Aumento: {{ $c->aumento_horimetro ?? '-' }}
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <button type="button"
                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                data-id="{{ $c->id }}" data-nome-veiculo="{{ $c->nome_veiculo }}"
                                data-placa="{{ $c->placa }}" data-numero-cartao="{{ $c->numero_cartao }}"
                                data-horimetro="{{ $c->horimetro }}"
                                data-aumento-horimetro="{{ $c->aumento_horimetro }}"
                                data-update-route="{{ route('cartao.update', $c->id) }}">
                                Alterar
                            </button>

                            <form action="{{ route('cartao.destroy', $c->id) }}" method="POST"
                                onsubmit="return confirmDelete(event, this, '{{ addslashes($c->nome_veiculo) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-gray-500">Nenhum cartão cadastrado.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $cartoes->links() }}
        </div>
    </div>

    {{-- Modal criar --}}
    <div id="modalCreateCartao" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Cadastrar Cartão</h2>
                <button type="button" id="closeModalCreate" class="text-gray-500 text-xl">&times;</button>
            </div>

            <form action="{{ route('cartao.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Nome do veículo</label>
                        <input type="text" name="nome_veiculo"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Placa</label>
                        <input type="text" name="placa"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Número do cartão</label>
                        <input type="text" name="numero_cartao"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600">Horímetro</label>
                            <input type="number" name="horimetro" min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Aumento do horímetro</label>
                            <input type="number" name="aumento_horimetro" min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" id="cancelCreate" class="px-4 py-2 border rounded-lg text-gray-700">
                        Cancelar
                    </button>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal editar --}}
    <div id="modalEditCartao" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Editar Cartão</h2>
                <button type="button" id="closeModalEdit" class="text-gray-500 text-xl">&times;</button>
            </div>

            <form id="formEditCartao" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Nome do veículo</label>
                        <input type="text" name="nome_veiculo" value="{{ old('nome_veiculo') }}"
                            id="edit_nome_veiculo"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Placa</label>
                        <input type="text" name="placa" value="{{ old('placa') }}" id="edit_placa"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Número do cartão</label>
                        <input type="text" name="numero_cartao" value="" id="edit_numero_cartao"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-gray-600">Horímetro</label>
                            <input type="number" name="horimetro" id="edit_horimetro" min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>

                        <div>
                            <label class="text-sm text-gray-600">Aumento do horímetro</label>
                            <input type="number" name="aumento_horimetro" id="edit_aumento_horimetro" min="0"
                                class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id" id="edit_id">

                <div class="flex justify-end gap-2 mt-5">
                    <button type="button" id="cancelEdit" class="px-4 py-2 border rounded-lg text-gray-700">
                        Cancelar
                    </button>

                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(event, form, nome) {
            event.preventDefault();
            if (confirm('Deseja realmente excluir o cartão "' + nome + '"?')) {
                form.submit();
            }
            return false;
        }

        const modalCreate = document.getElementById('modalCreateCartao');
        const modalEdit = document.getElementById('modalEditCartao');
        const formEdit = document.getElementById('formEditCartao');

        const btnOpenCreate = document.getElementById('btnOpenCreate');
        const closeModalCreate = document.getElementById('closeModalCreate');
        const cancelCreate = document.getElementById('cancelCreate');

        const closeModalEdit = document.getElementById('closeModalEdit');
        const cancelEdit = document.getElementById('cancelEdit');

        if (btnOpenCreate) {
            btnOpenCreate.addEventListener('click', function() {
                modalCreate.classList.remove('hidden');
                modalCreate.classList.add('flex');
            });
        }

        function closeCreate() {
            modalCreate.classList.add('hidden');
            modalCreate.classList.remove('flex');
        }

        function closeEdit() {
            modalEdit.classList.add('hidden');
            modalEdit.classList.remove('flex');
        }

        if (closeModalCreate) closeModalCreate.onclick = closeCreate;
        if (cancelCreate) cancelCreate.onclick = closeCreate;
        if (closeModalEdit) closeModalEdit.onclick = closeEdit;
        if (cancelEdit) cancelEdit.onclick = closeEdit;

        document.querySelectorAll('.btn-open-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                modalEdit.classList.remove('hidden');
                modalEdit.classList.add('flex');

                document.getElementById('edit_id').value = this.dataset.id ?? '';
                document.getElementById('edit_nome_veiculo').value = this.dataset.nomeVeiculo ?? '';
                document.getElementById('edit_placa').value = this.dataset.placa ?? '';
                document.getElementById('edit_numero_cartao').value = this.dataset.numeroCartao ?? '';
                document.getElementById('edit_horimetro').value = this.dataset.horimetro ?? 0;
                document.getElementById('edit_aumento_horimetro').value = this.dataset.aumentoHorimetro ??
                    0;
                formEdit.action = this.dataset.updateRoute;
            });
        });
    </script>
@endpush
