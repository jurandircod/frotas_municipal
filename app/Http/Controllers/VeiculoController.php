<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoVeiculo;
use App\Models\Veiculo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

class VeiculoController extends Controller
{
    /**
     * Mostrar formulário de cadastro de veículo.
     */
    public function index()
    {
        $tipoVeiculos = TipoVeiculo::all();

        return view('veiculo.veiculo', compact('tipoVeiculos'));
    }

    /**
     * Cadastrar novo veículo.
     */
    public function store(Request $request)
    {
        // validação (reusa o mesmo conjunto de regras)
        $this->validateRequest($request);

        $data = $request->all();
        Veiculo::create($data);

        return redirect()->back()->with('success', 'Veículo cadastrado com sucesso!');
    }

    /**
     * Lista paginada de veículos.
     */
    public function list()
    {
        $veiculos = Veiculo::with('tipoVeiculo')->orderBy('placa')->paginate(15);
        $tipoVeiculos = TipoVeiculo::orderBy('nome')->get();

        return view('veiculo.lista', compact('veiculos', 'tipoVeiculos'));
    }

    /**
     * Remove um veículo pelo id.
     */
    public function destroy($id)
    {
        $deleted = Veiculo::destroy($id);

        $message = $deleted
            ? 'Veículo deletado com sucesso!'
            : 'Veículo não encontrado ou já removido.';

        // manter a mesma chave flash 'success' do original para compatibilidade
        return redirect()->back()->with('success', $message);
    }

    /**
     * Edita um veículo existente.
     *
     * Observação: o original aceita o id via $request->id; mantive esse comportamento.
     */
    public function edit(Request $request)
    {
        try {
            $this->validateRequest($request, true);

            $veiculo = Veiculo::find($request->id);

            if (! $veiculo) {
                return redirect()->back()->with('error', 'Veículo não encontrado.');
            }

            $veiculo->update($request->all());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Veículo editado com sucesso!'
                ]);
            }

            return redirect()->back()->with('success', 'Veículo editado com sucesso!');
        } catch (ValidationException $e) {
            // Mantive o comportamento original que retorna os erros da validação
            return redirect()->back()->with('error', $e->errors());
        } catch (\Exception $e) {
            // Erro genérico — ajuda no diagnóstico sem vazar stack trace
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Regras de validação reutilizáveis.
     *
     * @param  Request  $request
     * @param  bool     $isUpdate  adiciona validação de 'id' quando true
     * @return void (lança ValidationException em caso de falha)
     */
    protected function validateRequest(Request $request, bool $isUpdate = false): void
    {
        $currentYear = Carbon::now()->year;

        $rules = [
            'placa' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'ano' => 'required|integer|min:1980|max:' . $currentYear,
            'cor' => 'required',
            'tipo_veiculo_id' => 'required',
            'combustivel' => 'required|in:gasolina,etanol,diesel,flex,eletrico',
            'km_atual' => 'required',
            'status' => 'required',
        ];

        if ($isUpdate) {
            // Para edição o código original exigia 'id'
            $rules['id'] = 'required';
        }

        $messages = [
            'placa.required' => 'A placa é obrigatória',
            'marca.required' => 'A marca é obrigatória',
            'modelo.required' => 'O modelo é obrigatório',
            'ano.required' => 'O ano é obrigatório',
            'ano.integer' => 'O ano deve ser um número inteiro',
            'ano.min' => 'O ano mínimo permitido é 1980',
            'ano.max' => "O ano não pode ser maior que {$currentYear}",
            'cor.required' => 'A cor é obrigatória',
            'tipo_veiculo_id.required' => 'O tipo de veículo é obrigatório',
            'combustivel.required' => 'O combustível é obrigatório',
            'combustivel.in' => 'Combustível inválido',
            'km_atual.required' => 'O km atual é obrigatório',
            'status.required' => 'O status é obrigatório',
            'id.required' => 'O id é obrigatório',
        ];

        $request->validate($rules, $messages);
    }
}