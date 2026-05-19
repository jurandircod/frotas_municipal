{{-- resources/views/retirada/entrega.blade.php --}}
@extends('layouts.app')

@section('title', 'Minhas Retiradas')
@section('page_header', 'Minhas Retiradas')

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
    @endphp

    <div class="mx-auto max-w-4xl px-4 py-6 space-y-4">
        <div class="rounded-2xl bg-white shadow-sm ring-1 ring-gray-200 overflow-hidden">
            <div class="border-b bg-blue-50 px-5 py-4">
                <h1 class="text-lg font-bold text-gray-900">Minhas Retiradas</h1>
                <p class="text-sm text-gray-600">Acompanhe o andamento e faça suas solicitações.</p>
            </div>

            <div class="p-5">
                <form method="GET" action="{{ route('retirada.entrega.index') }}" class="grid gap-3 md:grid-cols-3">
                    <select name="categoria" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
                        <option value="">Todas as categorias</option>
                        <option value="cartao" @selected(request('categoria') === 'cartao')>Cartões</option>
                        <option value="ferramenta" @selected(request('categoria') === 'ferramenta')>Ferramentas</option>
                        <option value="generico" @selected(request('categoria') === 'generico')>Genérico</option>
                        <option value="chaves" @selected(request('categoria') === 'chaves')>Chaves</option>
                    </select>

                    <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
                        <option value="">Todos os status</option>
                        <option value="pendente" @selected(request('status') === 'pendente')>Pendente</option>
                        <option value="retirado" @selected(request('status') === 'retirado')>Retirado</option>
                        <option value="pendente entrega" @selected(request('status') === 'pendente entrega')>Pendente de Entrega</option>
                        <option value="cancelar entrega" @selected(request('status') === 'cancelar entrega')>Cancelamento Solicitado</option>
                        <option value="entregue" @selected(request('status') === 'entregue')>Entregue</option>
                        <option value="cancelado" @selected(request('status') === 'cancelado')>Cancelado</option>
                        <option value="negado" @selected(request('status') === 'negado')>Negado</option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">
                            Filtrar
                        </button>
                        <a href="{{ route('retirada.entrega.index') }}" class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-3">
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

                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-sm font-bold text-gray-900">
                                {{ ucfirst($r->categoria ?? '-') }}
                            </h2>

                            <p class="mt-1 text-xs text-gray-600">
                                Item: <span class="font-medium text-gray-800">{{ $itemNome }}</span>
                            </p>

                            <p class="mt-1 text-xs text-gray-600">
                                Solicitado por: <span class="font-medium text-gray-800">{{ $r->user->name ?? '-' }}</span>
                            </p>
                        </div>

                        <span @class(['shrink-0 rounded-full px-3 py-1 text-[11px] font-bold', $badgeClass])>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="block text-[10px] uppercase tracking-wide text-gray-400">Retirada</span>
                            <span class="font-medium text-gray-700">
                                {{ $r->datahora_retirada ? \Carbon\Carbon::parse($r->datahora_retirada)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>

                        <div class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2">
                            <span class="block text-[10px] uppercase tracking-wide text-gray-400">Entrega</span>
                            <span class="font-medium text-gray-700">
                                {{ $r->datahora_entrega ? \Carbon\Carbon::parse($r->datahora_entrega)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-2 rounded-xl border border-gray-100 bg-gray-50 px-3 py-2 text-xs text-gray-600">
                        Autorizado por:
                        <span class="font-medium text-gray-800">{{ $r->retirada_autorizada_por ?? '-' }}</span>
                    </div>

                    @if ($status === 'pendente')
                        <div class="mt-4">
                            <form action="{{ route('retirada.cancelar', $r->id) }}" method="POST"
                                onsubmit="return confirm('Deseja realmente cancelar este pedido?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="w-full rounded-xl border border-red-300 bg-white px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition-all active:scale-95">
                                    Cancelar pedido
                                </button>
                            </form>
                        </div>
                    @elseif ($status === 'retirado')
                        <div class="mt-4 grid grid-cols-2 gap-2">
                            <form action="{{ route('retirada.cancelar.entrega', $r->id) }}" method="POST"
                                onsubmit="return confirm('Deseja realmente solicitar o cancelamento deste pedido?')">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="w-full rounded-xl border border-red-300 bg-white px-4 py-3 text-sm font-bold text-red-600 hover:bg-red-50 transition-all active:scale-95">
                                    Solicitar cancelamento
                                </button>
                            </form>

                            <form action="{{ route('retirada.entregar', $r->id) }}" method="POST"
                                onsubmit="return confirm('Deseja realmente solicitar a entrega deste pedido?')">
                                @csrf
                                @method('PUT')

                                <button type="submit"
                                    class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white hover:bg-green-700 transition-all active:scale-95">
                                    Solicitar entrega
                                </button>
                            </form>
                        </div>
                    @elseif ($status === 'pendente entrega')
                        <div class="mt-4 rounded-xl bg-orange-50 px-3 py-3 text-xs text-orange-800 border border-orange-100">
                            Sua solicitação de entrega está em análise.
                        </div>
                    @elseif ($status === 'entregue')
                        <div class="mt-4 rounded-xl bg-blue-50 px-3 py-3 text-xs text-blue-800 border border-blue-100">
                            Pedido entregue. Nenhuma alteração disponível.
                        </div>
                    @else
                        <div class="mt-4 rounded-xl bg-gray-50 px-3 py-3 text-xs text-gray-500 border border-gray-100">
                            Este pedido não permite mais alterações.
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-6 text-sm text-gray-500 shadow-sm">
                    Nenhum pedido encontrado.
                </div>
            @endforelse
        </div>

        <div class="pt-2">
            {{ $retiradas->links() }}
        </div>
    </div>
@endsection