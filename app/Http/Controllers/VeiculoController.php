<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoVeiculo;
use App\Models\Veiculo;

class VeiculoController extends Controller
{
    public function index()
    {
        $tipoVeiculos = TipoVeiculo::all();
        return view('veiculo.veiculo', compact('tipoVeiculos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // Correção 1: Adicionar aspas nas chaves e campos da regra unique
            'placa' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            // Correção 2: Adicionar aspas e ajustar a data atual corretamente
            'ano' => 'required|integer|min:1980|max:' . date('Y'),
            'cor' => 'required',
            'tipo_veiculo_id' => 'required',
            'combustivel' => 'required|in:gasolina,etanol,diesel,flex,eletrico',
            'km_atual' => 'required',
            'status' => 'required',
        ], [
            // Correção 3: Adicionar aspas nas chaves das mensagens
            'placa.required' => 'O placa é obrigatório',
            'marca.required' => 'A marca é obrigatória',
            'modelo.required' => 'O modelo é obrigatório',
            'ano.required' => 'O ano é obrigatório',
            'cor.required' => 'A cor é obrigatória',
            'tipo_veiculo_id.required' => 'O tipo de veículo é obrigatório',
            'combustivel.required' => 'O combustível é obrigatório',
        ]);

        $data = $request->all();
        Veiculo::create($data);

        return redirect()->back()->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function list()
    {
        $veiculos = Veiculo::with('tipoVeiculo')->orderBy('placa')->paginate(15);
        $tipoVeiculos = TipoVeiculo::orderBy('nome')->get();
        return view('veiculo.lista', compact('veiculos', 'tipoVeiculos'));
    }

    public function destroy($id)
    {
        Veiculo::destroy($id);
        return redirect()->back()->with('success', 'Veículo deletado com sucesso!');
    }

    public function edit(Request $request)
    {
        try {
            $request->validate(
                [
                    'placa' => 'required',
                    'id' => 'required',
                    'marca' => 'required',
                    'modelo' => 'required',
                    'ano' => 'required|integer|min:1980|max:' . date('Y'),
                    'cor' => 'required',
                    'tipo_veiculo_id' => 'required',
                    'combustivel' => 'required|in:gasolina,etanol,diesel,flex,eletrico',
                    'km_atual' => 'required',
                    'status' => 'required',
                ],
                [
                    'placa.required' => "placa é obrigatória",
                    'id.required' => "id é obrigatória",
                    'marca.required' => "marca é obrigatória",
                    'modelo.required' => "modelo é obrigatória",
                    'ano.required' => "ano é obrigatória",
                    'cor.required' => "cor é obrigatória",
                    'tipo_veiculo_id.required' => "tipo_veiculo_id é obrigatória",
                    'combustivel.required' => "combustivel é obrigatória",
                    'km_atual.required' => "km_atual é obrigatória",
                    'status.required' => "status é obrigatória",
                ]
            );

            $veiculo = Veiculo::find($request->id);
            $veiculo->update($request->all());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Veículo editado com sucesso!'
                ]);
            }
            return redirect()->back()->with('success', 'Veículo editado com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', $e->errors());
        }
    }
}
