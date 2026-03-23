@extends('layouts.app')
@section('content')
    <div class="min-h-screen bg-gradient-to-br from-green-50 via-white to-blue-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-xl">
            <div class="overflow-hidden rounded-3xl bg-white shadow-xl border border-green-100">
                <div class="from-green-500 to-emerald-600 px-6 py-8 text-yellow-500">
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20 ring-4 ring-white/20">
                            <span class="text-3xl" aria-hidden="true">✅</span>
                        </div>
                        <div>
                            <span
                                class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold tracking-wide">
                                Corrida registrada
                            </span>
                            <h1 class="mt-2 text-2xl font-bold sm:text-3xl">
                                Corrida iniciada com sucesso
                            </h1>
                            <p class="mt-1 text-sm text-green-50">
                                O veículo já está liberado para seguir o percurso.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8">
                    <div class="rounded-2xl bg-green-50 border border-green-100 p-4">
                        <p class="text-sm text-green-800">
                            Tudo certo. A corrida foi iniciada e os dados foram salvos com segurança.
                        </p>
                    </div>

                    @php
                        $mov = $movimentacao ?? null;
                    @endphp

                    <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Motorista</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ Auth::user()->name }}
                            </p>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Status</p>
                            <p class="mt-1 text-base font-semibold text-green-600">
                                Em andamento
                            </p>
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
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Veículo</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ $mov->veiculo->placa ?? '—' }}
                            </p>
                            @if (!empty($mov->veiculo->modelo))
                                <p class="text-sm text-gray-500">{{ $mov->veiculo->modelo }}</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">KM inicial</p>
                            <p class="mt-1 text-base font-semibold text-gray-800">
                                {{ isset($mov->km_inicial) ? number_format($mov->km_inicial, 1, ',', '.') : '—' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 p-4">
                        <p class="text-sm text-blue-900">
                            Próximo passo: acompanhe a corrida e finalize quando chegar ao destino.
                        </p>
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ url()->previous() }}"
                            class="inline-flex w-full items-center justify-center rounded-2xl bg-green-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-green-700">
                            Concluir a corrida
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
