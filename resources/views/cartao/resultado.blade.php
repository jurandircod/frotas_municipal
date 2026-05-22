@extends('layouts.app')

@section('title', 'Resultado da operação')
@section('page_header', 'Resultado da operação')

@section('content')
    @php
        $isSuccess = $status === 'success';
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">

        <div
            class="rounded-2xl border p-6 shadow-sm {{ $isSuccess ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div
                        class="h-12 w-12 rounded-full flex items-center justify-center {{ $isSuccess ? 'bg-green-100' : 'bg-red-100' }}">
                        @if ($isSuccess)
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        @endif
                    </div>
                </div>

                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xl font-semibold {{ $isSuccess ? 'text-green-800' : 'text-red-800' }}">
                            {{ $isSuccess ? 'Operação realizada com sucesso' : 'Falha na operação' }}
                        </h3>

                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $isSuccess ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $isSuccess ? 'Sucesso' : 'Erro' }}
                        </span>
                    </div>

                    <p class="mt-2 text-sm {{ $isSuccess ? 'text-green-700' : 'text-red-700' }}">
                        {{ $mensagem ?? 'Nenhuma mensagem foi informada.' }}
                    </p>
                </div>
            </div>
        </div>

        @isset($cartao)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h4 class="text-lg font-semibold text-gray-800">Cartão envolvido na operação</h4>
                    <p class="text-sm text-gray-500">Confira abaixo as informações do cartão retirado.</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">ID</span>
                            <span class="mt-1 block text-sm font-medium text-gray-800">{{ data_get($cartao, 'id', '-') }}</span>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Número</span>
                            <span class="mt-1 block text-sm font-medium text-gray-800">{{ data_get($cartao, 'numero', '-') }}</span>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Status</span>
                            <span class="mt-1 inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                {{ data_get($cartao, 'status') === 'inativo' ? 'bg-gray-100 text-gray-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ data_get($cartao, 'status', '-') }}
                            </span>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Placa</span>
                            <span class="mt-1 block text-sm font-medium text-gray-800">{{ data_get($cartao, 'placa', '-') }}</span>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Modelo</span>
                            <span class="mt-1 block text-sm font-medium text-gray-800">{{ data_get($cartao, 'modelo', '-') }}</span>
                        </div>

                        <div class="rounded-xl border border-gray-200 p-4">
                            <span class="block text-xs font-semibold text-gray-500 uppercase">Criado em</span>
                            <span class="mt-1 block text-sm font-medium text-gray-800">
                                {{ optional(data_get($cartao, 'created_at'))->format('d/m/Y H:i') ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-gray-50 border border-gray-200 p-4">
                        <p class="text-sm text-gray-700">
                            <strong>Resumo:</strong>
                            o cartão foi movido para o status <strong>{{ data_get($cartao, 'status', '-') }}</strong>
                            e a operação foi processada normalmente.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
                <p class="text-sm text-gray-500">
                    Nenhum cartão foi enviado para exibição.
                </p>
            </div>
        @endisset

        <div class="flex flex-wrap gap-3">
            <a href="{{ url()->previous() }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                Voltar
            </a>

            <a href="{{ route('cartao.index') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                Ir para cartões
            </a>
        </div>
    </div>
@endsection