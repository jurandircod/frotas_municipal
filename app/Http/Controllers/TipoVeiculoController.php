<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoVeiculo;

class TipoVeiculoController extends Controller
{
    public function index()
    {
        return view('tipo-veiculo.tipo-veiculo');
    }

    public function store(Request $request)
    {

        $request->validate(
            [
                'nome' => 'required',
            ],
            [
                'nome.required' => "nome é obrigatório"
            ]
        );
        TipoVeiculo::create($request->all());
        return redirect()->back()->with('success', 'Tipo do Veículo cadastrado com sucesso!');
    }

    public function list()
    {
        $tipos = TipoVeiculo::all();
        return view('tipo-veiculo.lista', compact('tipos'));
    }

    public function destroy($id)
    {
        TipoVeiculo::destroy($id);
        return redirect()->back()->with('success', 'Tipo do Veículo deletado com sucesso!');
    }

    public function edit(Request $request)
    {
        $request->validate(
            [
                'nome' => 'required',
                'id' => 'required',
            ],
            [
                'nome.required' => "nome é obrigatório",
                'id.required' => "id é obrigatório"
            ]
        );
        $tipo = TipoVeiculo::find($request->id);
        $tipo->update($request->all());

        return redirect()->back()->with('success', 'Tipo do Veículo editado com sucesso!');
    }
}
