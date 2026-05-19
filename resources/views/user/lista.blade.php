{{-- resources/views/motoristas/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Motoristas')
@section('page_header', 'Motoristas')
@section('page_actions')
    <a href="{{ route('user.store') }}"
        class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm">
        Cadastrar Motorista
    </a>
@endsection

@section('content')
    <div class="space-y-4">

        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700">Lista de Motoristas</h3>
                    <p class="text-xs text-gray-500">Visualize, edite ou exclua motoristas.</p>
                </div>
            </div>

            {{-- Filtros --}}
            <form method="GET" action="{{ route('user.list') }}" class="mt-4 bg-gray-50 border rounded-xl p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <input type="text" name="nome" value="{{ request('nome') }}" placeholder="Buscar por nome"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Secretaria</label>
                        <select name="secretaria_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todas</option>
                            @foreach ($secretarias as $s)
                                <option value="{{ $s->id }}" @selected(request('secretaria_id') == $s->id)>
                                    {{ $s->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">Todos</option>
                            <option value="ativo" @selected(request('status') == 'ativo')>Ativo</option>
                            <option value="licença" @selected(request('status') == 'licença')>Licença</option>
                            <option value="suspenso" @selected(request('status') == 'suspenso')>Suspenso</option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg shadow-sm text-sm hover:bg-blue-700">
                            Buscar
                        </button>

                        <a href="{{ route('user.list') }}"
                            class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">
                            Limpar
                        </a>
                    </div>
                </div>
            </form>
            {{-- Table (desktop) --}}
            <div class="mt-4 hidden md:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs text-gray-500 text-left">
                            <tr>
                                <th class="px-3 py-2">Nome</th>
                                <th class="px-3 py-2">CPF</th>
                                <th class="px-3 py-2">Telefone</th>
                                <th class="px-3 py-2">CNH (cat)</th>
                                <th class="px-3 py-2">Validade CNH</th>
                                <th class="px-3 py-2">Secretarias</th>
                                <th class="px-3 py-2 w-48">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $m)
                                <tr class="border-t">
                                    <td class="px-3 py-3 text-gray-800 font-medium">{{ $m->name }}</td>
                                    <td class="px-3 py-3">{{ $m->cpf ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $m->telefone ?? '-' }}</td>
                                    <td class="px-3 py-3">{{ $m->categoria ?? ($m->cnh_categoria ?? '-') }}</td>
                                    <td class="px-3 py-3">
                                        @if (!empty($m->validade_cnh ?? $m->cnh_validade))
                                            {{ \Carbon\Carbon::parse($m->validade_cnh ?? $m->cnh_validade)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">{{ $m->secretaria->nome ?? '-' }}</td>
                                    <td class="px-3 py-3">
                                        <div class="flex gap-2">

                                            <button type="button"
                                                class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm border"
                                                data-id="{{ $m->id }}" data-nome="{{ $m->name }}"
                                                data-cpf="{{ $m->cpf }}" data-telefone="{{ $m->telefone }}"
                                                data-email="{{ $m->email }}"
                                                data-categoria="{{ $m->categoria ?? ($m->cnh_categoria ?? '') }}"
                                                data-validade="{{ $m->validade_cnh ?? $m->cnh_validade }}"
                                                data-update-route="{{ route('user.store', $m->id) }}"
                                                data-role="{{ $m->role_id }}" data-secretaria="{{ $m->secretaria_id }}">
                                                Alterar
                                            </button>

                                            <form action="{{ route('user.destroy', $m->id) }}" method="POST"
                                                onsubmit="return confirmDelete(event, this, '{{ addslashes($m->name) }}')">
                                                @csrf
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
                                    <td class="px-3 py-4 text-gray-500" colspan="6">Nenhum motorista cadastrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Mobile cards --}}
            <div class="mt-4 md:hidden space-y-3">
                @forelse($users as $m)
                    <div class="border rounded-xl p-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ $m->nome }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $m->cpf ?? '-' }} • {{ $m->telefone ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">CNH:
                                    {{ $m->categoria ?? ($m->cnh_categoria ?? '-') }} • Validade:
                                    {{ !empty($m->validade_cnh ?? $m->cnh_validade) ? \Carbon\Carbon::parse($m->validade_cnh ?? $m->cnh_validade)->format('d/m/Y') : '-' }}
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <button type="button"
                                    class="btn-open-edit px-3 py-1 rounded-md bg-yellow-50 text-yellow-800 text-sm"
                                    data-id="{{ $m->id }}" data-nome="{{ $m->name }}"
                                    data-cpf="{{ $m->cpf }}" data-telefone="{{ $m->telefone }}"
                                    data-email="{{ $m->email }}" data-cnh="{{ $m->cnh ?? ($m->cnh_numero ?? '') }}"
                                    data-categoria="{{ $m->categoria ?? ($m->cnh_categoria ?? '') }}"
                                    data-validade="{{ optional($m->validade_cnh ?? $m->cnh_validade)->format('Y-m-d') ?? '' }}"
                                    data-nascimento="{{ optional($m->data_nascimento ?? $m->nascimento)->format('Y-m-d') ?? '' }}"
                                    data-endereco="{{ $m->endereco }}" data-status="{{ $m->status }}"
                                    data-observacoes="{{ $m->observacoes ?? '' }}"
                                    data-foto-url="{{ $m->foto_url ?? '' }}"
                                    data-update-route="{{ route('user.edit', $m->id) }}"
                                    data-role="{{ $m->role_id }}">Alterar</button>

                                <form action="{{ route('user.destroy', $m->id) }}" method="POST"
                                    onsubmit="return confirmDelete(event, this, '{{ addslashes($m->nome) }}')">
                                    @csrf
                                    <button type="submit" hidden
                                        class="px-3 py-1 rounded-md bg-red-50 text-red-700 text-sm">Excluir Conta</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">Nenhum motorista cadastrado.</div>
                @endforelse
            </div>

            {{-- Modal editar motorista --}}
            <div id="modalEditMotorista" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50">

                <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Editar Motorista</h2>
                        <button id="closeModalEdit" class="text-gray-500 text-xl">&times;</button>
                    </div>

                    <form id="formEditMotorista" method="POST">
                        @csrf
                        <div class="space-y-3">


                            <div class="row">
                                <div class="col">
                                    <label class="text-sm text-gray-600">Role</label>
                                    <select name="role_id" id="edit_role_id" class="w-full border rounded-lg px-3 py-2">
                                        <option value="2">Administrador</option>
                                        <option value="1">Motorista</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="text-sm text-gray-600">Secretarias</label>
                                    <select name="secretaria_id" id="edit_secretaria_id"
                                        class="w-full border rounded-lg px-3 py-2">
                                        @foreach ($secretarias as $s)
                                            <option value="{{ $s->id }}">{{ $s->nome }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div>
                                <label class="text-sm text-gray-600">Nome</label>
                                <input type="text" name="name" id="edit_nome"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Email</label>
                                <input type="text" name="email" id="edit_email"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">CPF</label>
                                <input type="text" name="cpf" id="edit_cpf"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">Telefone</label>
                                <input type="text" name="telefone" id="edit_telefone"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">Categoria CNH</label>
                                <input type="text" name="cnh_categoria" id="edit_categoria"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                            <div>
                                <label class="text-sm text-gray-600">Validade CNH</label>
                                <input type="date" name="cnh_validade" id="edit_validade"
                                    class="w-full border rounded-lg px-3 py-2">
                            </div>

                        </div>

                        <input type="" hidden name="id" id="edit_id">

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

            {{-- Pagination --}}
            @if (method_exists($users, 'links'))
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // Confirmação antes de excluir
        function confirmDelete(event, form, nome) {
            event.preventDefault();
            const ok = confirm('Deseja realmente excluir o motorista "' + nome + '"?');
            if (ok) form.submit();
            return false;
        }
    </script>
@endpush

@push('scripts')
    <script>
        function confirmDelete(event, form, nome) {
            event.preventDefault();
            if (confirm('Deseja realmente excluir o motorista "' + nome + '"?')) {
                form.submit();
            }
            return false;
        }

        const modal = document.getElementById('modalEditMotorista');
        const form = document.getElementById('formEditMotorista');

        document.querySelectorAll('.btn-open-edit').forEach(btn => {

            btn.addEventListener('click', function() {

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                document.getElementById('edit_nome').value = this.dataset.nome ?? '';
                document.getElementById('edit_cpf').value = this.dataset.cpf ?? '';
                document.getElementById('edit_telefone').value = this.dataset.telefone ?? '';
                document.getElementById('edit_categoria').value = this.dataset.categoria ?? '';
                document.getElementById('edit_validade').value = this.dataset.validade ?? '';
                document.getElementById('edit_id').value = this.dataset.id ?? '';
                document.getElementById('edit_email').value = this.dataset.email ?? '';
                document.getElementById('edit_role_id').value = this.dataset.role ?? '';
                document.getElementById('edit_secretaria_id').value = this.dataset.secretaria ?? '';
                form.action = this.dataset.updateRoute;

            });

        });

        document.getElementById('closeModalEdit').onclick = closeModal;
        document.getElementById('cancelEdit').onclick = closeModal;

        function closeModal() {
            modal.classList.add('hidden');
        }
    </script>
@endpush
