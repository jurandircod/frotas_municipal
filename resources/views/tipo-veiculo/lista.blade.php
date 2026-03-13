{{-- resources/views/tipos_veiculos/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tipos de Veículos')
@section('page_header', 'Tipos de Veículos')
@section('page_actions')
    <a href="{{ route('tipoVeiculo.store') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
        Cadastrar Tipo
    </a>
@endsection

@section('content')
    <div class="space-y-4">

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Lista de tipos</h3>
                    <p class="text-xs text-gray-500">Exibe apenas o nome do tipo de veículo.</p>
                </div>
            </div>

            {{-- Table (desktop) --}}
            <div class="mt-4 hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-500 text-left">
                            <tr>
                                <th class="px-3 py-2">Nome</th>
                                <th class="px-3 py-2 w-48">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tipos as $tipo)
                                <tr class="border-t">
                                    <td class="px-3 py-3 text-gray-800">{{ $tipo->nome }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex gap-2">
                                            <!-- BOTÃO ALTERAR: abre modal -->
                                            <button
                                                type="button"
                                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                                data-id="{{ $tipo->id }}"
                                                data-nome="{{ $tipo->nome }}"
                                                data-update-route="{{ route('tipoVeiculo.edit', $tipo->id) }}"
                                                {{-- se você realmente quiser abrir a rota edit (GET) em vez de update, coloque também: data-edit-route="{{ route('tipoVeiculo.edit', $tipo->id) }}" --}}
                                            >Alterar</button>

                                            <form action="{{ route('tipoVeiculo.destroy', $tipo->id) }}" method="POST"
                                                onsubmit="return confirmDelete(event, this, '{{ addslashes($tipo->nome) }}')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm border">Excluir</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3 py-4 text-gray-500" colspan="2">Nenhum tipo cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile cards --}}
            <div class="mt-4 md:hidden space-y-3">
                @forelse($tipos as $tipo)
                    <div class="border rounded-xl p-3 flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-800">{{ $tipo->nome }}</div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm"
                                data-id="{{ $tipo->id }}"
                                data-nome="{{ $tipo->nome }}"
                                data-update-route="{{ route('tipoVeiculo.edit', $tipo->id) }}"
                            >Alterar</button>

                            <form action="{{ route('tipoVeiculo.destroy', $tipo->id) }}" method="POST"
                                onsubmit="return confirmDelete(event, this, '{{ addslashes($tipo->nome) }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm">Excluir</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">Nenhum tipo cadastrado.</div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if(method_exists($tipos, 'links'))
                <div class="mt-4">
                    {{ $tipos->links() }}
                </div>
            @endif
        </div>

    </div>

    <!-- EDIT MODAL -->
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black opacity-40" data-close-modal></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Alterar Tipo de Veículo</h3>
                <button type="button" class="text-gray-500" data-close-modal aria-label="Fechar">✕</button>
            </div>

            <form id="editForm" method="POST" class="px-6 py-6">
                @csrf {{-- o método será PUT para rota update --}}
                <div>
                    <label class="block">
                        <span class="text-sm text-gray-700">Nome</span>
                        <input type="text" name="nome" id="editNome" required
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                            placeholder="Ex: Van">
                    </label>

                    <input type="text" name="id" id="editId" value="" hidden>
                </div>

                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-lg border" data-close-modal>Cancelar</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white">Salvar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Confirmação amigável antes de excluir
        function confirmDelete(event, form, nome) {
            event.preventDefault();
            const confirmed = confirm('Deseja realmente excluir o tipo "' + nome + '"? Esta ação não pode ser desfeita.');
            if (confirmed) {
                form.submit();
            }
            return false;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const editModal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const editNome = document.getElementById('editNome');
            const openButtons = document.querySelectorAll('.btn-open-edit');
            const closeTriggers = document.querySelectorAll('[data-close-modal]');
            let currentUpdateRoute = null;

            // Open modal and fill with data attributes
            openButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const nome = this.getAttribute('data-nome') || '';
                    const updateRoute = this.getAttribute('data-update-route');


                    // set form action to the update route for this record
                    editForm.action = updateRoute;
                    currentUpdateRoute = updateRoute;

                    // set the input value
                    editNome.value = nome;
                    editId.value = id;
                    // show modal
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                    // focus input for UX
                    setTimeout(() => editNome.focus(), 150);
                });
            });

            // Close modal handlers
            closeTriggers.forEach(el => {
                el.addEventListener('click', function () {
                    closeModal();
                });
            });

            // click outside to close
            editModal.addEventListener('click', function (e) {
                if (e.target === editModal) closeModal();
            });

            // esc to close
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && !editModal.classList.contains('hidden')) {
                    closeModal();
                }
            });

            function closeModal() {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
                // clear form action (optional)
                // editForm.action = '';
            }

            // Optional: client-side submit handler
            editForm.addEventListener('submit', function (e) {
                // basic validation
                if (!editNome.value.trim()) {
                    e.preventDefault();
                    alert('Informe um nome válido.');
                    editNome.focus();
                    return false;
                }

                // form will submit with method PUT to the update route
                // NOTE: server-side must accept and validate the request.
            });
        });
    </script>
@endpush