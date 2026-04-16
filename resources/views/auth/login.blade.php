@extends('layouts.app')
@section('content')
    <div class="min-h-[70vh] flex items-center justify-center">
        <div class="w-full max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">
                {{-- LADO ESQUERDO: Logo / Identidade --}}
                <div
                    class="p-8 bg-gradient-to-b from-blue-600 to-blue-700 text-white flex flex-col items-center justify-center">
                    {{-- Espaço para logo da prefeitura --}}
                    <div class="w-36 h-36 mb-4">
                        <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo Prefeitura"
                            onerror="this.src='{{ asset('images/logo-fallback.png') }}'; this.onerror=null;"
                            class="w-full h-full object-contain rounded-md bg-white p-2">
                    </div>

                    <h1 class="text-2xl font-semibold">{{ config('app.name', 'Prefeitura') }}</h1>
                    <p class="mt-2 text-sm opacity-90">Sistema de Gestão de Frota</p>

                    {{-- Texto opcional --}}
                    <p class="mt-6 text-xs text-white/80 text-center px-6">
                        Acesse o sistema com sua conta institucional. Caso tenha problemas, contate o suporte.
                    </p>
                </div>

                {{-- LADO DIREITO: Formulário --}}
                <div class="p-8">
                    {{-- Mensagem de status (Session) --}}
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <h2 class="text-lg font-medium text-gray-800 mb-4">Entrar na conta</h2>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        {{-- Senha --}}
                        <div>
                            <x-input-label for="password" :value="__('Senha')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                                autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        {{-- Lembrar-me --}}
                        <div class="flex items-center justify-between">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" checked
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    name="remember">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                            @endif
                        </div>

                        {{-- Botões --}}
                        <div class="flex items-center justify-end gap-3">
                            <x-primary-button class="inline-flex items-center justify-center px-6 py-2">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                        <a href="{{ route('password.request') }}" class="mt-6 text-center text-sm text-blue-600 hover:underline">
                            Esqueci a senha
                        </a>
                    </form>

                    <a href="{{ route('register') }}" class="mt-6 text-center text-sm text-blue-600 hover:underline">
                        Não tem conta? Crie uma nova
                    </a>

                    {{-- Pequeno rodapé no card --}}
                    <div class="mt-6 text-xs text-gray-500">
                        <span>Versão do sistema: <strong class="text-gray-700">1.0.0</strong></span>
                    </div>
                </div>
            </div>

            {{-- Link para voltar à home (opcional) --}}
            <div class="mt-6 text-center text-sm text-gray-600">
                <a href="{{ url('/') }}" class="hover:underline">Voltar para a página inicial</a>
            </div>
        </div>
    </div>
@endsection
