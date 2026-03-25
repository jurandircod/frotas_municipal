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

    public function store(Request $request)
    {
        
        $secretaria = Secretaria::create($request->all());
        return redirect()->route('secretarias.index')->with('success', 'Secretaria criada com sucesso!');
    }
}
