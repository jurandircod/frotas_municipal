@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-5 border-b">
                    <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">Cadastro de Motorista</h1>
                    <p class="text-sm text-gray-500 mt-1">Registre os dados do condutor — uso por gestor e motoristas</p>
                </div>

                <!-- Form -->
                <form id="motoristaForm" method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data"
                    class="px-6 py-6 space-y-6" novalidate>
                    @csrf

                    <!-- Nome -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Nome Completo *</span>
                            <input name="name" id="nome" type="text"
                                value="{{ old('nome') ?? Auth::user()->name }} "
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required aria-required="true" placeholder="Fulano de Tal">
                        </label>
                        <span>
                            @if ($errors->has('name'))
                                <span class="text-sm text-red-600">{{ $errors->first('name') }}</span>
                            @endif
                        </span>
                    </div>

                    <!-- CPF + Telefone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">CPF *</span>
                            <input name="cpf" id="cpf" type="text" inputmode="numeric" maxlength="14"
                                value="{{ old('cpf') ?? Auth::user()->cpf }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="000.000.000-00" required>
                                <span>
                                    @if ($errors->has('cpf'))
                                        <span class="text-sm text-red-600">{{ $errors->first('cpf') }}</span>
                                    @endif
                                </span>
                        </label>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Telefone</span>
                            <input name="telefone" id="telefone" type="tel" inputmode="tel" maxlength="15"
                                value="{{ old('telefone') ?? Auth::user()->telefone }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="(00) 90000-0000">
                        </label>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Email</span>
                            <input name="email" id="email" type="email"
                                value="{{ old('email') ?? Auth::user()->email }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="exemplo@dominio.com">

                                <span>
                                    @if ($errors->has('email'))
                                        <span class="text-sm text-red-600">{{ $errors->first('email') }}</span>
                                    @endif
                                </span>
                        </label>
                    </div>

                    <!-- CNH: Nº, Categoria, Validade -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">CNH Nº *</span>
                            <input name="cnh_numero" id="cnh_numero" type="text"
                                value="{{ old('cnh_numero') ?? Auth::user()->cnh_numero }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="12345678900" required>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Categoria *</span>
                            <select name="cnh_categoria" id="cnh_categoria"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                                <option value="">-- selecione --</option>
                                @if(isset(Auth::user()->cnh_categoria))
                                    <option value="{{ Auth::user()->cnh_categoria }}" selected>{{ Auth::user()->cnh_categoria }}</option>
                                @endif
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="AB">AB</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                                <option value="E">E</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Validade CNH *</span>
                            <input name="cnh_validade" id="cnh_validade" type="date"
                                value="{{ old('cnh_validade') ?? Auth::user()->cnh_validade }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                        </label>
                    </div>

                    <!-- Data de nascimento / Endereço -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Data de Nascimento</span>
                            <input name="nascimento" id="nascimento" type="date"
                                value="{{ old('nascimento') ?? Auth::user()->nascimento }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Endereço</span>
                            <input name="endereco" id="endereco" type="text"
                                value="{{ old('endereco') ?? Auth::user()->endereco }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="Rua, número, bairro">
                        </label>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">Status *</span>
                            <select name="status" id="status"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                                <option value="ativo">Ativo</option>
                                <option value="licenca">Em Licença</option>
                                <option value="suspenso">Suspenso</option>
                            </select>
                        </label>
                    </div>

                    <!-- validation / messages -->
                    <div id="errorMsg" class="text-sm text-red-600" role="alert" aria-live="polite"></div>

                    <!-- Desktop action row -->
                    <div class="sm:flex items-center justify-between mt-2">
                        <div class="text-sm text-gray-500">Campos com * são obrigatórios</div>
                        <div class="flex gap-3">
                            <a href="" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Voltar</a>
                            <button id="submitBtn" type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white shadow-sm">Salvar Alteração</button>
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
            const form = document.getElementById('motoristaForm');
            const cpf = document.getElementById('cpf');
            const telefone = document.getElementById('telefone');
            const foto = document.getElementById('foto');
            const fotoPreview = document.getElementById('fotoPreview');
            const fotoPlaceholder = document.getElementById('fotoPlaceholder');
            const cnhValidade = document.getElementById('cnh_validade');
            const submitBtnMobile = document.getElementById('submitBtnMobile');
            const errorMsg = document.getElementById('errorMsg');

            // Simple input masks (CPF & Telefone)
            function maskCPF(v) {
                v = v.replace(/\D/g, '');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                return v;
            }

            function maskPhone(v) {
                v = v.replace(/\D/g, '');
                v = v.replace(/^0+/, '');
                if (v.length > 10) {
                    v = v.replace(/(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
                } else if (v.length > 5) {
                    v = v.replace(/(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                } else if (v.length > 2) {
                    v = v.replace(/(\d{2})(\d{0,5})/, '($1) $2');
                } else {
                    v = v.replace(/(\d*)/, '($1');
                }
                return v;
            }

            cpf.addEventListener('input', function(e) {
                const pos = this.selectionStart;
                this.value = maskCPF(this.value);
                this.setSelectionRange(pos, pos);
            });

            telefone.addEventListener('input', function(e) {
                const pos = this.selectionStart;
                this.value = maskPhone(this.value);
                this.setSelectionRange(pos, pos);
            });

            // Foto preview
            foto.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    fotoPreview.src = '';
                    fotoPreview.classList.add('hidden');
                    fotoPlaceholder.classList.remove('hidden');
                    return;
                }
                if (!file.type.startsWith('image/')) return;
                if (file.size > 2 * 1024 * 1024) { // 2MB
                    errorMsg.textContent = 'Imagem muito grande. Máx 2MB.';
                    this.value = '';
                    return;
                }
                errorMsg.textContent = '';
                const reader = new FileReader();
                reader.onload = function(ev) {
                    fotoPreview.src = ev.target.result;
                    fotoPreview.classList.remove('hidden');
                    fotoPlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            });

            // CNH validade check on submit
            function validadeOK() {
                if (!cnhValidade.value) return true; // handled by required if empty
                const validade = new Date(cnhValidade.value + 'T00:00:00');
                const hoje = new Date();
                // set time to 00:00 to avoid TZ issues
                hoje.setHours(0, 0, 0, 0);
                return validade >= hoje;
            }

            function trySubmit() {
                errorMsg.textContent = '';
                // basic required checks
                const nome = document.getElementById('nome').value.trim();
                const cnh = document.getElementById('cnh_numero').value.trim();
                const categoria = document.getElementById('cnh_categoria').value;

                if (!nome) {
                    errorMsg.textContent = 'Informe o nome do motorista.';
                    document.getElementById('nome').focus();
                    return;
                }
                if (!cnh) {
                    errorMsg.textContent = 'Informe o número da CNH.';
                    document.getElementById('cnh_numero').focus();
                    return;
                }
                if (!categoria) {
                    errorMsg.textContent = 'Selecione a categoria da CNH.';
                    document.getElementById('cnh_categoria').focus();
                    return;
                }

                if (!validadeOK()) {
                    errorMsg.textContent = 'CNH com validade expirada. Verifique a data.';
                    cnhValidade.focus();
                    return;
                }

                // tudo ok — envia
                form.submit();
            }

            if (submitBtnMobile) submitBtnMobile.addEventListener('click', trySubmit);

            // desktop submit: let native submit occur (server-side must revalidate)
            form.addEventListener('submit', function(e) {
                if (!validadeOK()) {
                    e.preventDefault();
                    errorMsg.textContent = 'CNH com validade expirada. Verifique a data.';
                    cnhValidade.focus();
                }
            });
        });
    </script>
@endpush
