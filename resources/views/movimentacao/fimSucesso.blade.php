@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-8 text-green sm:px-8">
                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/20">
                            <span class="text-3xl" aria-hidden="true">🏁</span>
                        </div>

                        <div>
                            <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide">
                                Corrida concluída
                            </span>
                            <h1 class="mt-2 text-2xl font-bold text-green sm:text-3xl">
                                Corrida finalizada com sucesso
                            </h1>
                            <p class="mt-1 text-sm text-emerald-50">
                                O encerramento foi registrado corretamente no sistema.
                            </p>
                        </div>
                    </div>
                </div>

                @php
                    $mov = $movimentacao ?? null;
                @endphp

                <div class="px-6 py-6 sm:px-8">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
                        <p class="text-sm font-medium text-emerald-900">
                            A corrida foi encerrada e os dados foram salvos com sucesso.
                        </p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Motorista</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ Auth::user()->name }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Status</p>
                            <p class="mt-1 text-base font-semibold text-emerald-600">
                                Finalizada
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Veículo</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ $mov->veiculo->placa ?? '—' }}
                            </p>
                            @if (!empty($mov->veiculo->modelo))
                                <p class="text-sm text-gray-500">{{ $mov->veiculo->modelo }}</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Origem</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ $mov->origem ?? '—' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Destino</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ $mov->destino ?? '—' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">KM inicial</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ isset($mov->km_inicial) ? number_format($mov->km_inicial, 1, ',', '.') : '—' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">KM final</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ isset($mov->km_final) ? number_format($mov->km_final, 1, ',', '.') : '—' }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">KM rodado</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ isset($mov->km_rodado) ? number_format($mov->km_rodado, 1, ',', '.') : '—' }}
                            </p>
                        </div>
                    </div>

                    @if (!empty($mov->observacao))
                        <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Observações</p>
                            <p class="mt-1 text-sm text-slate-700">
                                {{ $mov->observacao }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-4">
                        <p class="text-sm text-blue-900">
                            A corrida foi encerrada corretamente. Você já pode iniciar uma nova movimentação quando necessário.
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('movimentacao.index') }}"
                           class="inline-flex w-full items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                            Voltar para corridas
                        </a>

                        <a href="{{ url()->previous() }}"
                           class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Retornar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection