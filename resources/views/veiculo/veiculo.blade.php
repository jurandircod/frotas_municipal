@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden">

                <!-- Header -->
                <div class="px-6 py-5 border-b">
                    <h1 class="text-lg sm:text-2xl font-semibold text-gray-800">
                        Cadastro de Veículo
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Informe os dados do veículo da frota
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{route('veiculo.store')}}" class="px-6 py-6 space-y-6">
                    @csrf
                    <!-- Placa -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">
                                Placa *
                            </span>
                            <input type="text" name="placa" id="placa" value="{{ old('placa') }}" maxlength="8"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-blue-400"
                                placeholder="ABC-1234" required>
                        </label>
                        @error('placa')
                            <p class="text-sm text-red-500 mt-2" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Modelo + Marca -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Marca *
                                </span>
                                <input type="text" name="marca" value="{{ old('marca') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                    required placeholder="Digite a Marca">
                            </label>
                            @error('marca')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Modelo *
                                </span>
                                <input type="text" name="modelo" value="{{ old('modelo') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                    required placeholder="Digite o modelo">
                            </label>
                            @error('modelo')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Ano + Cor -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Ano *
                                </span>
                                <input type="number" name="ano" value="{{ old('ano') }}" min="1980"
                                    max="{{ date('Y') + 1 }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                    required placeholder="Digite o Ano">
                            </label>
                            @error('ano')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Cor
                                </span>
                                <input type="text" name="cor" placeholder="Digite a Cor" value="{{ old('cor') }}"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400">
                            </label>
                            @error('cor')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Tipo + Combustível -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Tipo de Veículo *
                                </span>
                                <select name="tipo_veiculo_id"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                    required>
                                    <option value="">-- selecione --</option>
                                    @foreach ($tipoVeiculos as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('tipo_veiculo_id')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block">
                                <span class="text-sm font-medium text-gray-700">
                                    Combustível *
                                </span>

                                <select name="combustivel"
                                    class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                    required>
                                    <option value="">-- selecione --</option>
                                    <option value="gasolina">Gasolina</option>
                                    <option value="etanol">Etanol</option>
                                    <option value="diesel">Diesel</option>
                                    <option value="flex">Flex</option>
                                    <option value="eletrico">Elétrico</option>
                                </select>
                            </label>
                            @error('combustivel')
                                <p class="text-sm text-red-500 mt-2" role="alert">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- KM Atual -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">
                                KM Atual *
                            </span>
                            <input type="number" name="km_atual" step="0.1" value="{{ old('km_atual') }}"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                required placeholder="Insira o km atual">
                        </label>
                        @error('km_atual')
                            <p class="text-sm text-red-500 mt-2" role="alert">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block">
                            <span class="text-sm font-medium text-gray-700">
                                Status *
                            </span>
                            <select name="status"
                                class="mt-1 w-full rounded-lg border-gray-300 shadow-sm py-3 px-3 focus:ring-2 focus:ring-blue-400"
                                required>
                                <option value="ativo">Ativo</option>
                                <option value="manutencao">Em Manutenção</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </label>
                        @error('status')
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
                            <a href="" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 rounded-lg bg-blue-600 text-white shadow-sm">
                                Salvar Veículo
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Mobile fixed button -->

    </div>
@endsection