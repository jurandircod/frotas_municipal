{{-- resources/views/retirada/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Retiradas')
@section('page_header', 'Solicitar Retirada')

@section('content')
    @php
        $statusMap = [
            'pendente' => ['bg-amber-100 text-amber-700', 'Pendente'],
            'retirado' => ['bg-violet-100 text-violet-700', 'Retirado'],
            'pendente entrega' => ['bg-orange-100 text-orange-700', 'Pendente de Entrega'],
            'cancelar entrega' => ['bg-zinc-100 text-zinc-700', 'Cancelamento Solicitado'],
            'entregue' => ['bg-blue-100 text-blue-700', 'Entregue'],
            'cancelado' => ['bg-gray-100 text-gray-700', 'Cancelado'],
            'negado' => ['bg-red-100 text-red-700', 'Negado'],
        ];

        $categoriaSelecionada = old('categoria', $categoriaPadrao ?? null);
        $categoriaBloqueada = filled($categoriaPadrao);
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-6 space-y-4">
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <div class="font-semibold">Corrija os campos abaixo.</div>
            </div>
        @endif

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="border-b bg-blue-50 px-5 py-4">
                <h1 class="text-lg font-bold text-gray-900">Nova retirada</h1>
                <p class="text-sm text-gray-600">Selecione a categoria e preencha apenas o campo necessário.</p>
            </div>

            <form action="{{ route('retirada.store') }}" method="POST" class="p-5 space-y-5">
                @csrf

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="md:col-span-2 {{ $categoriaBloqueada ? 'hidden' : '' }}">
                        <label for="categoriaSelect" class="mb-1 block text-sm font-medium text-gray-700">
                            Categoria
                        </label>

                        <select
                            name="categoria"
                            id="categoriaSelect"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none"
                            required
                        >
                            <option value="">Selecione</option>
                            <option value="cartao" @selected($categoriaSelecionada === 'cartao')>Cartões</option>
                            <option value="ferramenta" @selected($categoriaSelecionada === 'ferramenta')>Ferramentas</option>
                            <option value="generico" @selected($categoriaSelecionada === 'generico')>Retirada genérica</option>
                            <option value="chaves" @selected($categoriaSelecionada === 'chaves')>Chaves</option>
                        </select>

                        @error('categoria')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="campoCartao" class="hidden md:col-span-2 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <label for="cartao_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Selecione o cartão
                        </label>
                        <select
                            name="cartao_id"
                            id="cartao_id"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">Selecione</option>
                            @foreach ($cartoes as $cartao)
                                <option value="{{ $cartao->id }}" @selected(old('cartao_id') == $cartao->id)>
                                    {{ $cartao->nome_veiculo }} - {{ $cartao->placa }} - {{ $cartao->numero_cartao }}
                                </option>
                            @endforeach
                        </select>
                        @error('cartao_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="campoFerramenta" class="hidden md:col-span-2 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <label for="ferramenta_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Selecione a ferramenta
                        </label>
                        <select
                            name="ferramenta_id"
                            id="ferramenta_id"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none"
                        >
                            <option value="">Selecione</option>
                            @foreach ($ferramentas as $ferramenta)
                                <option value="{{ $ferramenta->id }}" @selected(old('ferramenta_id') == $ferramenta->id)>
                                    {{ $ferramenta->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('ferramenta_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="campoNomeGenerico" class="hidden md:col-span-2 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <label for="inputNomeGenerico" id="labelNomeGenerico" class="mb-1 block text-sm font-medium text-gray-700">
                            Nome do item
                        </label>
                        <input
                            type="text"
                            name="nome_generico"
                            id="inputNomeGenerico"
                            value="{{ old('nome_generico') }}"
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none"
                            placeholder="Digite o nome do item"
                        >
                        @error('nome_generico')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                    A solicitação ficará com status pendente até análise.
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button
                        type="submit"
                        class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition"
                    >
                        Enviar solicitação
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="border-b bg-gray-50 px-5 py-4">
                <h2 class="text-base font-bold text-gray-900">Meus pedidos</h2>
                <p class="text-sm text-gray-600">Acompanhe o andamento de cada solicitação.</p>
            </div>

            <div class="space-y-3 p-5">
                @forelse ($retiradas as $r)
                    @php
                        $status = strtolower($r->status ?? 'pendente');
                        [$badgeClass, $statusLabel] = $statusMap[$status] ?? $statusMap['pendente'];

                        $itemNome = match ($r->categoria) {
                            'cartao' => $r->cartao->nome_veiculo ?? '-',
                            'ferramenta' => $r->ferramenta->nome ?? '-',
                            default => $r->nome_generico ?? '-',
                        };
                    @endphp

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-gray-900">
                                    {{ ucfirst($r->categoria ?? '-') }}
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    Item: <span class="font-medium text-gray-700">{{ $itemNome }}</span>
                                </div>

                                <div class="mt-1 text-xs text-gray-500">
                                    Solicitado por: <span class="font-medium text-gray-700">{{ $r->user->name ?? '-' }}</span>
                                </div>
                            </div>

                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $badgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-500">
                            <div class="rounded-xl border border-gray-100 bg-white px-3 py-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-400">Retirada</span>
                                <span class="font-medium text-gray-700">
                                    {{ $r->datahora_retirada ? \Carbon\Carbon::parse($r->datahora_retirada)->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>

                            <div class="rounded-xl border border-gray-100 bg-white px-3 py-2">
                                <span class="block text-[10px] uppercase tracking-wide text-gray-400">Entrega</span>
                                <span class="font-medium text-gray-700">
                                    {{ $r->datahora_entrega ? \Carbon\Carbon::parse($r->datahora_entrega)->format('d/m/Y H:i') : '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-2 rounded-xl border border-gray-100 bg-white px-3 py-2 text-xs text-gray-500">
                            Autorizado por:
                            <span class="font-medium text-gray-700">{{ $r->retirada_autorizada_por ?? '-' }}</span>
                        </div>

                        @if (($r->status ?? 'pendente') === 'pendente' && empty($r->retirada_autorizada_por))
                            <form
                                action="{{ route('retirada.cancelar', $r->id) }}"
                                method="POST"
                                onsubmit="return confirm('Deseja realmente cancelar este pedido?')"
                                class="mt-3"
                            >
                                @csrf
                                @method('PUT')

                                <button
                                    type="submit"
                                    class="w-full rounded-xl border border-red-300 bg-white px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition"
                                >
                                    Cancelar pedido
                                </button>
                            </form>
                        @else
                            <div class="mt-3 text-center text-xs text-gray-400">
                                Este pedido não pode mais ser cancelado.
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-500">
                        Nenhum pedido encontrado.
                    </div>
                @endforelse
            </div>

            <div class="px-5 pb-5">
                {{ $retiradas->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const categoriaSelect = document.getElementById('categoriaSelect');
                const campoCartao = document.getElementById('campoCartao');
                const campoFerramenta = document.getElementById('campoFerramenta');
                const campoNomeGenerico = document.getElementById('campoNomeGenerico');
                const labelNomeGenerico = document.getElementById('labelNomeGenerico');
                const inputNomeGenerico = document.getElementById('inputNomeGenerico');

                if (!categoriaSelect) return;

                function atualizarCampos() {
                    const categoria = categoriaSelect.value;

                    campoCartao.classList.add('hidden');
                    campoFerramenta.classList.add('hidden');
                    campoNomeGenerico.classList.add('hidden');

                    if (categoria === 'cartao') {
                        campoCartao.classList.remove('hidden');
                    }

                    if (categoria === 'ferramenta') {
                        campoFerramenta.classList.remove('hidden');
                    }

                    if (categoria === 'generico') {
                        labelNomeGenerico.textContent = 'Nome genérico';
                        inputNomeGenerico.placeholder = 'Digite o nome do item genérico';
                        campoNomeGenerico.classList.remove('hidden');
                    }

                    if (categoria === 'chaves') {
                        labelNomeGenerico.textContent = 'Nome do veículo da chave';
                        inputNomeGenerico.placeholder = 'Digite o nome do veículo';
                        campoNomeGenerico.classList.remove('hidden');
                    }
                }

                categoriaSelect.addEventListener('change', atualizarCampos);
                atualizarCampos();
            });
        </script>
    @endpush
@endsection