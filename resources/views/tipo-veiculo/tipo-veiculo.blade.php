@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto">
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b">
                <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">
                    Cadastro de tipo de veículo
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Informe os dados do tipo de veículo
                </p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{route('tipoVeiculo.store')}}" 
                  class="px-6 py-6 space-y-6">
                @csrf
                <!-- Placa -->
                <div>
                    <label class="block">
                        <span class="text-sm font-medium text-gray-700">
                           Nome <span class="text-red-500">*</span>
                        </span>
                        <input type="text" name="nome" id="nome" 
                            value="{{ old('nome') }}"
                            class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 tracking-widest focus:outline-none"
                            placeholder="Insira o nome do tipo de veiculo"
                            >
                    </label>
                    @error('nome')
                        <p class="text-sm text-red-500 mt-2" role="alert">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Desktop Actions -->
                <div class="sm:flex justify-between items-center pt-4">
                    <div class="text-sm text-gray-500">
                        Campos com * são obrigatórios
                    </div>
                    <div class="flex gap-3">
                        <a href=""
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">
                           Cancelar
                        </a>
                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-600 text-white shadow-sm">
                                Salvar 
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection