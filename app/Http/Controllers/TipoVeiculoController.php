<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoVeiculo;
use Illuminate\Validation\ValidationException;

class TipoVeiculoController extends Controller
{
    /**
     * Exibe a view de cadastro de tipo de veículo.
     */
    public function index()
    {
        return view('tipo-veiculo.tipo-veiculo');
    }

    /**
     * Armazena um novo tipo de veículo.
     */
    public function store(Request $request)
    {
        $this->validateRequest($request);

        TipoVeiculo::create($request->all());

        return redirect()->back()->with('success', 'Tipo do Veículo cadastrado com sucesso!');
    }

    /**
     * Lista todos os tipos de veículo.
     */
    public function list()
    {
        // Mantive a mesma saída (coleção) — ordenei por nome para melhor UX sem alterar chaves.
        $tipos = TipoVeiculo::orderBy('nome')->get();

        return view('tipo-veiculo.lista', compact('tipos'));
    }

    /**
     * Remove um tipo de veículo pelo id.
     */
    public function destroy($id)
    {
        $deleted = TipoVeiculo::destroy($id);

        $message = $deleted
            ? 'Tipo do Veículo deletado com sucesso!'
            : 'Tipo do Veículo não encontrado ou já removido.';

        // Mantive a chave 'success' para compatibilidade com flash messages existentes.
        return redirect()->back()->with('success', $message);
    }

    /**
     * Edita um tipo de veículo existente.
     *
     * Observação: o controller original espera o id em $request->id; mantive esse comportamento.
     */
    public function edit(Request $request)
    {
        try {
            $this->validateRequest($request, true);

            $tipo = TipoVeiculo::find($request->id);

            if (! $tipo) {
                return redirect()->back()->with('error', 'Tipo do Veículo não encontrado.');
            }

            $tipo->update($request->all());

            return redirect()->back()->with('success', 'Tipo do Veículo editado com sucesso!');
        } catch (ValidationException $e) {
            // Mantendo comportamento semelhante ao original: retornar os erros de validação
            return redirect()->back()->with('error', $e->errors());
        } catch (\Exception $e) {
            // Erro genérico para ajudar no diagnóstico sem vazar stack trace
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Regras e mensagens de validação reutilizáveis.
     *
     * @param  Request  $request
     * @param  bool     $isUpdate  Se true, exige também 'id'
     * @return void (lança ValidationException se falhar)
     */
    protected function validateRequest(Request $request, bool $isUpdate = false): void
    {
        $rules = [
            'nome' => 'required',
        ];

        if ($isUpdate) {
            // No fluxo de edição o código original exigia 'id'
            $rules['id'] = 'required';
        }

        $messages = [
            'nome.required' => 'O nome é obrigatório',
            'id.required' => 'O id é obrigatório',
        ];

        $request->validate($rules, $messages);
    }
}