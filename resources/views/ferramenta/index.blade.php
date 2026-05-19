@extends('layouts.app')

@section('title', 'Ferramentas')
@section('page_header', 'Ferramentas')

@section('page_actions')
    <button type="button" id="btnOpenCreate"
        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
        Cadastrar Ferramenta
    </button>
@endsection

@section('content')
    <div class="space-y-4">

        {{-- Filtros --}}
        <form method="GET" action="{{ route('ferramenta.list') }}" class="bg-gray-50 border rounded-xl p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                    <input type="text" name="nome" value="{{ request('nome') }}"
                        placeholder="Buscar por nome"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                    <input type="text" name="descricao" value="{{ request('descricao') }}"
                        placeholder="Buscar por descrição"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm hover:bg-blue-700">
                        Buscar
                    </button>

                    <a href="{{ route('ferramenta.list') }}"
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
                            <th class="px-3 py-2">Nome</th>
                            <th class="px-3 py-2">Descrição</th>
                            <th class="px-3 py-2 w-48">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ferramentas as $f)
                            <tr class="border-t">
                                <td class="px-3 py-3 text-gray-800 font-medium">{{ $f->nome }}</td>
                                <td class="px-3 py-3">{{ $f->descricao ?? '-' }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex gap-2">
                                        <button type="button"
                                            class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                            data-id="{{ $f->id }}"
                                            data-nome="{{ $f->nome }}"
                                            data-descricao="{{ $f->descricao }}"
                                            data-update-route="{{ route('ferramenta.update', $f->id) }}">
                                            Alterar
                                        </button>

                                        <form action="{{ route('ferramenta.destroy', $f->id) }}" method="POST"
                                            onsubmit="return confirmDelete(event, this, '{{ addslashes($f->nome) }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">
                                                Excluir
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="px-3 py-4 text-gray-500" colspan="3">Nenhuma ferramenta cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden space-y-3">
            @forelse($ferramentas as $f)
                <div class="border rounded-xl p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-medium text-gray-800">{{ $f->nome }}</div>
                            <div class="text-xs text-gray-500 mt-1">{{ $f->descricao ?? '-' }}</div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <button type="button"
                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                data-id="{{ $f->id }}"
                                data-nome="{{ $f->nome }}"
                                data-descricao="{{ $f->descricao }}"
                                data-update-route="{{ route('ferramenta.update', $f->id) }}">
                                Alterar
                            </button>

                            <form action="{{ route('ferramenta.destroy', $f->id) }}" method="POST"
                                onsubmit="return confirmDelete(event, this, '{{ addslashes($f->nome) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-gray-500">Nenhuma ferramenta cadastrada.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $ferramentas->links() }}
        </div>
    </div>

    {{-- Modal criar --}}
    <div id="modalCreateFerramenta" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Cadastrar Ferramenta</h2>
                <button type="button" id="closeModalCreate" class="text-gray-500 text-xl">&times;</button>
            </div>

            <form action="{{ route('ferramenta.store') }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Nome</label>
                        <input type="text" name="nome"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Descrição</label>
                        <textarea name="descricao" rows="4"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
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
    <div id="modalEditFerramenta" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Editar Ferramenta</h2>
                <button type="button" id="closeModalEdit" class="text-gray-500 text-xl">&times;</button>
            </div>

            <form id="formEditFerramenta" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-3">
                    <div>
                        <label class="text-sm text-gray-600">Nome</label>
                        <input type="text" name="nome" id="edit_nome"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <div>
                        <label class="text-sm text-gray-600">Descrição</label>
                        <textarea name="descricao" id="edit_descricao" rows="4"
                            class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
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
            if (confirm('Deseja realmente excluir a ferramenta "' + nome + '"?')) {
                form.submit();
            }
            return false;
        }

        const modalCreate = document.getElementById('modalCreateFerramenta');
        const modalEdit = document.getElementById('modalEditFerramenta');
        const formEdit = document.getElementById('formEditFerramenta');

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
                document.getElementById('edit_nome').value = this.dataset.nome ?? '';
                document.getElementById('edit_descricao').value = this.dataset.descricao ?? '';
                formEdit.action = this.dataset.updateRoute;
            });
        });
    </script>
@endpush