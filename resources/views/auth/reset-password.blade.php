@extends('layouts.app')

@section('title', 'Redefinir senha')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center py-8">
        <div class="w-full max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">

                {{-- LADO ESQUERDO --}}
                <div class="p-8 bg-gradient-to-b from-blue-600 to-blue-700 text-white flex flex-col items-center justify-center">
                    <div class="w-36 h-36 mb-4">
                        <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo Prefeitura"
                            onerror="this.src='{{ asset('images/logo-fallback.png') }}'; this.onerror=null;"
                            class="w-full h-full object-contain rounded-md bg-white p-2">
                    </div>

                    <h1 class="text-2xl font-semibold">{{ config('app.name', 'Prefeitura') }}</h1>
                    <p class="mt-2 text-sm opacity-90">Sistema de Gestão de Frota</p>

                    <p class="mt-6 text-xs text-white/80 text-center px-6">
                        Crie uma nova senha para concluir a recuperação da sua conta.
                    </p>
                </div>

                {{-- LADO DIREITO --}}
                <div class="p-8">
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <h2 class="text-lg font-medium text-gray-800 mb-1">Redefinir senha</h2>
                    <p class="text-sm text-gray-500 mb-6">
                        Confirme seu e-mail e escolha uma nova senha.
                    </p>

                    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input
                                id="email"
                                class="block mt-1 w-full"
                                type="email"
                                name="email"
                                :value="old('email', $email)"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Nova senha')" />
                            <x-text-input
                                id="password"
                                class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirmar nova senha')" />
                            <x-text-input
                                id="password_confirmation"
                                class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-2">
                            <a href="{{ route('login') }}"
                               class="text-sm text-gray-600 hover:text-gray-800 hover:underline">
                                Voltar ao login
                            </a>

                            <x-primary-button class="inline-flex items-center justify-center px-6 py-2">
                                {{ __('Redefinir senha') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection