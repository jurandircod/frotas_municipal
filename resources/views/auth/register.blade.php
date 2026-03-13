@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">

            {{-- LADO ESQUERDO: Identidade visual / logo --}}
            <div class="p-8 bg-gradient-to-b from-blue-600 to-blue-700 text-white flex flex-col items-center justify-center">
                {{-- Espaço para logo da prefeitura --}}
                <div class="w-36 h-36 mb-4">
                    <img src="{{ asset('images/logo-prefeitura.png') }}" alt="Logo Prefeitura"
                         onerror="this.src='{{ asset('images/logo-fallback.png') }}'; this.onerror=null;"
                         class="w-full h-full object-contain rounded-md bg-white p-2">
                </div>

                <h1 class="text-2xl font-semibold">{{ config('app.name', 'Prefeitura') }}</h1>
                <p class="mt-2 text-sm opacity-90">Sistema de Gestão de Frota</p>

                <p class="mt-6 text-xs text-white/80 text-center px-6">
                    Preencha os dados abaixo para criar sua conta institucional. Se tiver dúvida, contate o suporte.
                </p>
            </div>

            {{-- LADO DIREITO: Formulário de registro --}}
            <div class="p-8">
                {{-- Mensagem de status (Session) --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <h2 class="text-lg font-medium text-gray-800 mb-4">Criar nova conta</h2>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    {{-- Nome --}}
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                      :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                      :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Senha --}}
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password"
                                      required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    {{-- Confirmação de senha --}}
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                                      name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    {{-- Acordo / Observação opcional --}}
                    <div class="text-xs text-gray-500">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="terms" class="rounded border-gray-300 mr-2" required>
                            <span>Declaro que os dados inseridos são verdadeiros.</span>
                        </label>
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center justify-between mt-2">
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('login') }}">
                            {{ __('Already registered?') }}
                        </a>

                        <x-primary-button class="inline-flex items-center justify-center px-6 py-2">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>

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