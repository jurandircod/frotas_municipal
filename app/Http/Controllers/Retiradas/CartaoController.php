<?php

namespace App\Http\Controllers\Retiradas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cartao;

class CartaoController extends Controller
{
    public function index()
    {
        $cartoes = Cartao::paginate(10);
        return view('cartao.index', compact('cartoes'));
    }

    public function list(Request $request)
    {
        $cartoes = Cartao::query()
            ->when($request->filled('nome_veiculo'), function ($query) use ($request) {
                $query->where('nome_veiculo', 'like', '%' . $request->nome_veiculo . '%');
            })
            ->when($request->filled('placa'), function ($query) use ($request) {
                $query->where('placa', 'like', '%' . $request->placa . '%');
            })
            ->when($request->filled('numero_cartao'), function ($query) use ($request) {
                $query->where('numero_cartao', 'like', '%' . $request->numero_cartao . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('cartao.index', compact('cartoes'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nome_veiculo' => 'required|string|max:100',
                'placa' => 'required|string|max:10|unique:cartoes,placa',
                'numero_cartao' => 'required|string|max:50|unique:cartoes,numero_cartao',
                'horimetro' => 'nullable|integer|min:0',
                'aumento_horimetro' => 'nullable|integer|min:0',
            ],
            [
                'nome_veiculo.required' => 'O nome do veículo é obrigatório.',
                'placa.required' => 'A placa é obrigatória.',
                'placa.unique' => 'Essa placa já está cadastrada.',
                'numero_cartao.required' => 'O número do cartão é obrigatório.',
                'numero_cartao.unique' => 'Esse número de cartão já está cadastrado.',
                'horimetro.integer' => 'O horímetro deve ser um número inteiro.',
                'horimetro.min' => 'O horímetro não pode ser negativo.',
                'aumento_horimetro.integer' => 'O aumento do horímetro deve ser um número inteiro.',
                'aumento_horimetro.min' => 'O aumento do horímetro não pode ser negativo.',
            ]
        );

        Cartao::create($request->only([
            'nome_veiculo',
            'placa',
            'numero_cartao',
            'horimetro',
            'aumento_horimetro',
        ]));

        return redirect()->route('cartao.index')->with('success', 'Cartão cadastrado com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $cartao = Cartao::findOrFail($id);

        $request->validate(
            [
                'nome_veiculo' => 'required|string|max:100',
                'placa' => 'required|string|max:10|unique:cartoes,placa,' . $cartao->id,
                'numero_cartao' => 'required|string|max:50|unique:cartoes,numero_cartao,' . $cartao->id,
                'horimetro' => 'nullable|integer|min:0',
                'aumento_horimetro' => 'nullable|integer|min:0',
            ],
            [
                'nome_veiculo.required' => 'O nome do veículo é obrigatório.',
                'placa.required' => 'A placa é obrigatória.',
                'placa.unique' => 'Essa placa já está cadastrada.',
                'numero_cartao.required' => 'O número do cartão é obrigatório.',
                'numero_cartao.unique' => 'Esse número de cartão já está cadastrado.',
                'horimetro.integer' => 'O horímetro deve ser um número inteiro.',
                'horimetro.min' => 'O horímetro não pode ser negativo.',
                'aumento_horimetro.integer' => 'O aumento do horímetro deve ser um número inteiro.',
                'aumento_horimetro.min' => 'O aumento do horímetro não pode ser negativo.',
            ]
        );

        $cartao->update($request->only([
            'nome_veiculo',
            'placa',
            'numero_cartao',
            'horimetro',
            'aumento_horimetro',
        ]));

        return redirect()->route('cartao.index')->with('success', 'Cartão atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $cartao = Cartao::findOrFail($id);
        $cartao->delete();
        return redirect()->route('cartao.index')->with('success', 'Cartão excluído com sucesso!');
    }
}
