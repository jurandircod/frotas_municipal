@extends('layouts.app')

@section('title', 'Autorização de Retiradas')
@section('page_header', 'Autorização de Retiradas')

@section('content')
@php
    $statusMap = [
        'pendente' => ['bg-amber-100 text-amber-700', 'Pendente'],
        'pendente entrega' => ['bg-orange-100 text-orange-700', 'Pendente Entrega'],
        'cancelar entrega' => ['bg-zinc-100 text-zinc-700', 'Cancelar Entrega'],
        'retirado' => ['bg-violet-100 text-violet-700', 'Retirado'],
        'entregue' => ['bg-blue-100 text-blue-700', 'Entregue'],
        'cancelado' => ['bg-gray-100 text-gray-700', 'Cancelado'],
        'negado' => ['bg-red-100 text-red-700', 'Negado'],
    ];
@endphp

<div class="mx-auto max-w-5xl px-4 py-6">
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
        <div class="border-b bg-amber-50 px-5 py-4">
            <h1 class="text-lg font-bold text-gray-900">Autorizar Retiradas</h1>
            <p class="text-sm text-gray-600">Painel exclusivo para administradores</p>
        </div>

        <div class="p-5 space-y-4">
            <form method="GET" action="{{ route('retirada.autorizacao.index') }}" class="grid gap-3 rounded-2xl border bg-gray-50 p-4 md:grid-cols-3">
                <select name="categoria" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
                    <option value="">Todas as categorias</option>
                    <option value="cartao" @selected(request('categoria') === 'cartao')>Cartões</option>
                    <option value="ferramenta" @selected(request('categoria') === 'ferramenta')>Ferramentas</option>
                    <option value="generico" @selected(request('categoria') === 'generico')>Genérico</option>
                    <option value="chaves" @selected(request('categoria') === 'chaves')>Chaves</option>
                </select>

                <select name="status" class="rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm">
                    <option value="">Todos os status</option>
                    <option value="pendente" @selected(request('status') === 'pendente')>Pendentes</option>
                    <option value="pendente entrega" @selected(request('status') === 'pendente entrega')>Pendentes de Entrega</option>
                    <option value="negado" @selected(request('status') === 'negado')>Negados</option>
                    <option value="cancelado" @selected(request('status') === 'cancelado')>Cancelados</option>
                    <option value="cancelar entrega" @selected(request('status') === 'cancelar entrega')>Cancelar Entrega</option>
                    <option value="entregue" @selected(request('status') === 'entregue')>Entregues</option>
                    <option value="retirado" @selected(request('status') === 'retirado')>Retirados</option>
                </select>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white">
                        Filtrar
                    </button>
                    <a href="{{ route('retirada.autorizacao.index') }}" class="flex-1 rounded-xl border border-gray-300 px-4 py-3 text-center text-sm font-semibold text-gray-700">
                        Limpar
                    </a>
                </div>
            </form>

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

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-sm font-bold text-gray-900">{{ ucfirst($r->categoria ?? '-') }}</h2>
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

                        <div class="mt-2 rounded-xl border border-gray-100 bg-white px-3 py-2 text-xs text-gray-600">
                            Autorizado por: <span class="font-medium text-gray-800">{{ $r->retirada_autorizada_por ?? '-' }}</span>
                        </div>

                        @if ($r->status === 'pendente')
                            <div class="mt-4 grid grid-cols-2 gap-2">
                                <form action="{{ route('retirada.autorizacao', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="retirado">
                                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white">
                                        Autorizar
                                    </button>
                                </form>

                                <form action="{{ route('retirada.autorizacao', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="negado">
                                    <button type="submit" class="w-full rounded-xl bg-red-600 px-4 py-3 text-sm font-bold text-white">
                                        Negar
                                    </button>
                                </form>
                            </div>
                        @elseif ($r->status === 'cancelar entrega')
                            <div class="mt-4">
                                <form action="{{ route('retirada.cancelar', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white">
                                        Autorizar Cancelamento
                                    </button>
                                </form>
                            </div>
                        @elseif ($r->status === 'pendente entrega')
                            <div class="mt-4">
                                <form action="{{ route('retirada.concluir', $r->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="w-full rounded-xl bg-green-600 px-4 py-3 text-sm font-bold text-white">
                                        Autorizar Entrega
                                    </button>
                                </form>
                            </div>
                        @else
                            <p class="mt-4 text-center text-xs text-gray-400">Esse pedido já foi processado.</p>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-6 text-sm text-gray-500">
                        Nenhum pedido encontrado.
                    </div>
                @endforelse
            </div>

            <div class="pt-2">
                {{ $retiradas->links() }}
            </div>
        </div>
    </div>
</div>
@endsection