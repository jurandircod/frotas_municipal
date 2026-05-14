{{-- resources/views/secretarias/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Secretarias')
@section('page_header', 'Secretarias')
@section('subtitle', 'Gerencie as secretarias cadastradas no sistema')

@section('page_actions')
    <a href="{{ route('secretaria.index') }}"
        class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition">
        Nova Secretaria
    </a>
@endsection

@section('content')
    <div class="space-y-6">

        <div class="rounded-2xl bg-white shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-100 px-6 py-5 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </div>

                    <div class="flex-1">
                        <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Lista de Secretarias</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Visualize, altere ou exclua secretarias cadastradas.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Desktop --}}
            <div class="hidden md:block px-6 py-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-left text-xs text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-3">Nome</th>
                                <th class="py-3 px-3">Descrição</th>
                                <th class="py-3 px-3 w-56">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($secretarias as $secretaria)
                                <tr class="hover:bg-gray-50/70">
                                    <td class="py-4 px-3 font-medium text-gray-800">
                                        {{ $secretaria->nome }}
                                    </td>
                                    <td class="py-4 px-3 text-gray-600">
                                        {{ $secretaria->descricao ?: '—' }}
                                    </td>
                                    <td class="py-4 px-3">
                                        <div class="flex gap-2">
                                            <button type="button"
                                                class="btn-open-edit px-4 py-2 rounded-xl border border-yellow-200 bg-yellow-50 text-yellow-800 hover:bg-yellow-100 transition text-sm font-medium"
                                                data-id="{{ $secretaria->id }}" data-nome="{{ $secretaria->nome }}"
                                                data-descricao="{{ $secretaria->descricao }}"
                                                data-update-route="{{ route('secretaria.edit',"id=$secretaria->id") }}">
                                                Alterar
                                            </button>

                                            <form action="{{ route('secretaria.destroy', $secretaria->id) }}" method="POST"
                                                onsubmit="return confirmDelete(event, this, '{{ addslashes($secretaria->nome) }}')">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition text-sm font-medium">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-gray-500">
                                        Nenhuma secretaria cadastrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (method_exists($secretarias, 'links'))
                    <div class="mt-6">
                        {{ $secretarias->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile --}}
            <div class="md:hidden px-4 py-5 space-y-3">
                @forelse ($secretarias as $secretaria)
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h2 class="text-base font-semibold text-gray-800 truncate">{{ $secretaria->nome }}</h2>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $secretaria->descricao ?: 'Sem descrição' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <button type="button"
                                class="btn-open-edit flex-1 px-4 py-2 rounded-xl border border-yellow-200 bg-yellow-50 text-yellow-800 hover:bg-yellow-100 transition text-sm font-medium"
                                data-id="{{ $secretaria->id }}" data-nome="{{ $secretaria->nome }}"
                                data-descricao="{{ $secretaria->descricao }}"
                                data-update-route="{{ route('secretaria.edit', "id = $secretaria->id") }}">
                                Alterar
                            </button>

                            <form action="{{ route('secretaria.destroy', $secretaria->id) }}" method="POST" class="flex-1"
                                onsubmit="return confirmDelete(event, this, '{{ addslashes($secretaria->nome) }}')">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition text-sm font-medium">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-gray-100 bg-white shadow-sm p-6 text-center text-gray-500">
                        Nenhuma secretaria cadastrada.
                    </div>
                @endforelse

                @if (method_exists($secretarias, 'links'))
                    <div class="pt-2">
                        {{ $secretarias->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de edição --}}
    <div id="editSecretariaModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" data-close-modal></div>

        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden">
            <div
                class="border-b border-gray-100 px-6 py-4 bg-gradient-to-r from-blue-50 to-white flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">Alterar Secretaria</h3>
                    <p class="text-sm text-gray-500 mt-1">Edite as informações e salve as alterações.</p>
                </div>

                <button type="button" class="text-gray-500 hover:text-gray-800" data-close-modal aria-label="Fechar">
                    ✕
                </button>
            </div>

            <form id="editSecretariaForm" method="POST" class="px-6 py-6 space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome da Secretaria <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nome" id="edit_nome"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-3 px-4"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição
                    </label>
                    <textarea name="descricao" id="edit_descricao" rows="4"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-3 px-4"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="button"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition"
                        data-close-modal>
                        Cancelar
                    </button>

                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition">
                        Salvar Alterações
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
            const ok = confirm('Deseja realmente excluir a secretaria "' + nome + '"?');
            if (ok) form.submit();
            return false;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editSecretariaModal');
            const form = document.getElementById('editSecretariaForm');
            const inputNome = document.getElementById('edit_nome');
            const inputDescricao = document.getElementById('edit_descricao');
            const openButtons = document.querySelectorAll('.btn-open-edit');
            const closeTriggers = document.querySelectorAll('[data-close-modal]');

            function openModal() {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            openButtons.forEach(button => {
                button.addEventListener('click', function() {
                    form.action = this.dataset.updateRoute || '';
                    inputNome.value = this.dataset.nome || '';
                    inputDescricao.value = this.dataset.descricao || '';
                    openModal();
                    setTimeout(() => inputNome.focus(), 150);
                });
            });

            closeTriggers.forEach(el => {
                el.addEventListener('click', closeModal);
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        });
    </script>
@endpush
