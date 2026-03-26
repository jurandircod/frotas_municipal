@extends('layouts.app')

@section('title', 'Cadastrar Secretaria')
@section('page_header', 'Secretarias')
@section('subtitle', 'Cadastre uma nova secretaria para organizar o sistema')

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
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Cadastro de Secretaria</h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Registre uma nova secretaria com nome e descrição opcional.
                    </p>
                </div>
            </div>
        </div>

        <form id="formSecretaria" method="POST" action="{{ route('secretarias.store') }}" class="px-6 py-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nome da Secretaria <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nome"
                        value="{{ old('nome') }}"
                        placeholder="Ex: Secretaria de Saúde"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-3 px-4"
                        required
                    >
                    @error('nome')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Descrição
                    </label>
                    <textarea
                        name="descricao"
                        rows="4"
                        placeholder="Descreva a função ou responsabilidade da secretaria"
                        class="w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 py-3 px-4"
                    >{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="hidden sm:flex items-center justify-between pt-2">
                <p class="text-sm text-gray-500">Campos com * são obrigatórios</p>

                <div class="flex gap-3">
                    <a href="{{ route('secretaria.index') }}"
                       class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                        Cancelar
                    </a>

                    <button type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 text-white shadow-sm hover:bg-blue-700 transition">
                        Salvar Secretaria
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="rounded-2xl bg-blue-50 border border-blue-100 px-5 py-4">
        <div class="flex items-start gap-3">
            <div class="mt-0.5 h-9 w-9 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-blue-900">Dica rápida</h2>
                <p class="text-sm text-blue-800 mt-1">
                    Use nomes curtos e objetivos. A descrição ajuda a identificar a função da secretaria no sistema.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="fixed inset-x-0 bottom-0 sm:hidden z-50 border-t border-gray-200 bg-white/95 backdrop-blur px-4 py-3 safe-area">
    <div class="max-w-xl mx-auto flex gap-3">
        <a href="{{ route('secretaria.index') }}"
           class="flex-1 text-center py-3 rounded-xl border border-gray-300 text-gray-700 font-medium bg-white">
            Cancelar
        </a>

        <button type="button"
            onclick="document.getElementById('formSecretaria').submit()"
            class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-medium shadow-sm">
            Salvar
        </button>
    </div>
</div>
@endsection