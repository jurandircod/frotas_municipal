<?php

namespace App\Http\Controllers\Retiradas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ferramenta;

class FerramentasController extends Controller
{
    public function index()
    {
        $ferramentas = Ferramenta::orderBy('id', 'desc')->paginate(10);
        return view('ferramenta.index', compact('ferramentas'));
    }

    public function list(Request $request)
    {
        $ferramentas = Ferramenta::query()
            ->when($request->filled('nome'), function ($query) use ($request) {
                $query->where('nome', 'like', '%' . $request->nome . '%');
            })
            ->when($request->filled('descricao'), function ($query) use ($request) {
                $query->where('descricao', 'like', '%' . $request->descricao . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('ferramenta.index', compact('ferramentas'));
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nome' => 'required|string|max:150',
                'descricao' => 'nullable|string',
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.string' => 'O nome deve ser um texto válido.',
                'nome.max' => 'O nome não pode ter mais de 150 caracteres.',
                'descricao.string' => 'A descrição deve ser um texto válido.',
            ]
        );

        Ferramenta::create($request->only([
            'nome',
            'descricao',
        ]));

        return redirect()->route('ferramenta.list')
            ->with('success', 'Ferramenta cadastrada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $ferramenta = Ferramenta::findOrFail($id);

        $request->validate(
            [
                'nome' => 'required|string|max:150',
                'descricao' => 'nullable|string',
            ],
            [
                'nome.required' => 'O nome é obrigatório.',
                'nome.string' => 'O nome deve ser um texto válido.',
                'nome.max' => 'O nome não pode ter mais de 150 caracteres.',
                'descricao.string' => 'A descrição deve ser um texto válido.',
            ]
        );

        $ferramenta->update($request->only([
            'nome',
            'descricao',
        ]));

        return redirect()->route('ferramenta.list')
            ->with('success', 'Ferramenta atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $ferramenta = Ferramenta::findOrFail($id);
        $ferramenta->delete();

        return redirect()->route('ferramenta.list')
            ->with('success', 'Ferramenta excluída com sucesso!');
    }
}