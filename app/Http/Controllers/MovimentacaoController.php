<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class MovimentacaoController extends Controller
{
    public function index()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $veiculos = Veiculo::all();
        $user = Auth::user();
        $movimentacao = Movimentacao::where('status', 'ativa')->where('user_id', $user->id)
            ->with('veiculo', 'user')
            ->get();
        return view('movimentacao.movimentacao', compact('veiculos', 'user', 'movimentacao'));
    }

    public function withVeiculo($veiculoId)
    {
        date_default_timezone_set('America/Sao_Paulo');
        $veiculos = Veiculo::where('id', $veiculoId)
            ->with('tipoVeiculo')
            ->get();
        $user = Auth::user();
        $movimentacao = Movimentacao::where('status', 'ativa')
            ->with('veiculo', 'user')
            ->get();

        return view('movimentacao.movimentacao', compact('veiculos', 'user', 'movimentacao', 'veiculoId'));
    }

    public function list()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $veiculos = Veiculo::paginate(10);
        $users = User::paginate(10);
        $movimentacoes = Movimentacao::with('veiculo', 'user')->paginate(10);
        return view('movimentacao.lista', compact('veiculos', 'users', 'movimentacoes'));
    }

    public function cancelar($id)
    {
        Movimentacao::destroy($id);
        return redirect()->back()->with('success', 'Movimentação cancelada com sucesso!');
    }
    
    
    public function update(Request $request, $id)
    {
        
        $request->validate(
            [
                'data' => 'required',
                'hora' => 'required',
                'veiculo_id' => 'required',
                'user_id' => 'required',
                'tipo_combustivel' => 'required',
                'km_rodado' => 'required',
                'origem' => 'required',
                'status' => 'required',
                'destino' => 'required',
                'km_final' => 'required',
            ],
            [
                'data.required' => 'O data é obrigatório',
                'hora.required' => 'O hora é obrigatório',
                'veiculo_id.required' => 'O veiculo é obrigatório',
                'user_id.required' => 'O motorista é obrigatório',
                'tipo_combustivel.required' => 'O tipo de combustível é obrigatório',
                'km_rodado.required' => 'O km rodado é obrigatório',
                'origem.required' => 'O origem é obrigatório',
                'status.required' => 'O status é obrigatório',
                'destino.required' => 'O destino é obrigatório',
                'km_final.required' => 'O km final é obrigatório',
            ]
        );
        $movimentacao = Movimentacao::find($id);
        $veiculo = Veiculo::find($movimentacao->veiculo_id);
        $veiculo->km_atual = $request->km_final;
        $veiculo->save();
        $movimentacao->update($request->all());
        return redirect()->back()->with('success', 'Movimentação editada com sucesso!');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'data' => 'required',
                'hora' => 'required',
                'veiculo_id' => 'required',
                'user_id' => 'required',
                'tipo_combustivel' => 'required',
                'km_inicial' => 'required',
                'km_rodado' => 'required',
                'origem' => 'required',
                'status' => 'required',
            ],
            [
                'data.required' => 'O data é obrigatório',
                'hora.required' => 'O hora é obrigatório',
                'veiculo_id.required' => 'O veiculo é obrigatório',
                'user_id.required' => 'O motorista é obrigatório',
                'tipo_combustivel.required' => 'O tipo de combustível é obrigatório',
                'km_inicial.required' => 'O km inicial é obrigatório',
                'km_rodado.required' => 'O km rodado é obrigatório',
                'origem.required' => 'O origem é obrigatório',
                'status.required' => 'O status é obrigatório',
            ]
        );

        $movimentacao = Movimentacao::create($request->all());
        return redirect()->back()->with('success', 'Movimentação Iniciada com sucesso!');
    }
}
