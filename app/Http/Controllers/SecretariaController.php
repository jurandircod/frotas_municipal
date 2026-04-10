<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Secretaria;

class SecretariaController extends Controller
{
    public function index()
    {
        return view('secretaria.index');
    }

    public function list()
    {
        $secretarias = Secretaria::paginate();
        return view('secretaria.list', compact('secretarias'));
    }


    public function destroy($id)
    {
        $deleted = Secretaria::destroy($id);
        if (!$deleted) {
            return redirect()->route('secretaria.list')->with('error', 'Erro ao deletar secretaria!');
        }
        return redirect()->route('secretaria.list')->with('success', 'Secretaria deletada com sucesso!');
    }
    public function store(Request $request)
    {

        $secretaria = Secretaria::create($request->all());
        return redirect()->route('secretaria.list')->with('success', 'Secretaria criada com sucesso!');
    }
}
