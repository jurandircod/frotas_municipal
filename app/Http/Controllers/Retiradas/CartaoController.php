<?php

namespace App\Http\Controllers\Retiradas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cartao;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;



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

    public function gerarQrCode(Cartao $cartao)
    {
        if ($cartao->cartao_qr_code && Storage::disk('public')->exists($cartao->cartao_qr_code)) {
            return back()->with('info', 'Esse cartão já possui QR Code.');
        }

        $cartao->gerarQrCode();

        return back()->with('success', 'QR Code gerado com sucesso!');
    }

    public function gerarQrCodeRetirada(Cartao $cartao)
    {
        $cartao->gerarQrCodeRetirada();

        return back()->with('success', 'QR Code de retirada gerado com sucesso!');
    }

    public function gerarQrCodeEntrega(Cartao $cartao)
    {
        $cartao->gerarQrCodeEntrega();

        return back()->with('success', 'QR Code de entrega gerado com sucesso!');
    }
    public function show(Cartao $cartao)
    {
        return view('cartoes.show', compact('cartao'));
    }

    public function regenerarQrCode(Cartao $cartao)
    {
        $cartao->gerarQrCode();

        return back()->with('success', 'QR Code recriado com sucesso!');
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'nome_veiculo' => 'required|string|max:100',
            'placa' => 'required|string|max:10|unique:cartoes,placa',
            'numero_cartao' => 'required|string|max:50|unique:cartoes,numero_cartao',
            'horimetro' => 'nullable|integer|min:0',
            'aumento_horimetro' => 'nullable|integer|min:0',
        ], [
            'nome_veiculo.required' => 'O nome do veículo é obrigatório.',
            'placa.required' => 'A placa é obrigatória.',
            'placa.unique' => 'Essa placa já está cadastrada.',
            'numero_cartao.required' => 'O número do cartão é obrigatório.',
            'numero_cartao.unique' => 'Esse número de cartão já está cadastrado.',
            'horimetro.integer' => 'O horímetro deve ser um número inteiro.',
            'horimetro.min' => 'O horímetro não pode ser negativo.',
            'aumento_horimetro.integer' => 'O aumento do horímetro deve ser um número inteiro.',
            'aumento_horimetro.min' => 'O aumento do horímetro não pode ser negativo.',
        ]);

        if ($validator->fails()) {
            $primeiroErro = $validator->errors()->first();
            return redirect()->back()
                ->with('error', 'Erro - ' . $primeiroErro)
                ->withErrors($validator)
                ->withInput();
        }
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

        $validator = Validator::make(
            $request->all(),
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

        if ($validator->fails()) {
            $primeiroErro = $validator->errors()->first();
            return redirect()->back()
                ->with('error', 'Erro - ' . $primeiroErro)
                ->withErrors($validator)
                ->withInput();
        }

        // ✅ CORREÇÃO: Prepara os dados antes do update
        $dados = $request->only([
            'nome_veiculo',
            'placa',
            'numero_cartao',
            'horimetro',
            'aumento_horimetro',
        ]);

        // ✅ Converte null para 0 (usando integer, não string)
        $dados['aumento_horimetro'] = $dados['aumento_horimetro'] ?? 0;
        $dados['horimetro'] = $dados['horimetro'] ?? 0;

        // ✅ Agora sim atualiza com os dados tratados
        $cartao->update($dados);

        return redirect()->route('cartao.index')->with('success', 'Cartão atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $cartao = Cartao::findOrFail($id);
        $cartao->delete();
        return redirect()->route('cartao.index')->with('success', 'Cartão excluído com sucesso!');
    }
}
