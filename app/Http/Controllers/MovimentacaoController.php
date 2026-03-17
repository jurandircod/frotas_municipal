<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Veiculo;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class MovimentacaoController extends Controller
{
    public function __construct()
    {
        // centraliza timezone (mantém o comportamento do original)
        date_default_timezone_set('America/Sao_Paulo');
    }

    /**
     * Página de iniciar movimentação: lista veículos, usuário e movimentações ativas do usuário.
     */
    public function index()
    {
        $veiculos = Veiculo::all();
        $user = Auth::user();

        $movimentacao = Movimentacao::where('status', 'ativa')
            ->where('user_id', $user->id)
            ->with('veiculo', 'user')
            ->get();

        return view('movimentacao.movimentacao', compact('veiculos', 'user', 'movimentacao'));
    }

    /**
     * Mesma view, mas com um veículo filtrado (por id).
     */
    public function withVeiculo($veiculoId)
    {
        $veiculos = Veiculo::where('id', $veiculoId)
            ->with('tipoVeiculo')
            ->get();

        $user = Auth::user();

        $movimentacao = Movimentacao::where('status', 'ativa')
            ->where('user_id', $user->id)
            ->with('veiculo', 'user')
            ->get();

        return view('movimentacao.movimentacao', compact('veiculos', 'user', 'movimentacao', 'veiculoId'));
    }

    /**
     * Lista administrativa paginada.
     */
    public function list()
    {
        $veiculos = Veiculo::paginate(10);
        $users = User::paginate(10);
        $movimentacoes = Movimentacao::with('veiculo', 'user')->paginate(10);

        return view('movimentacao.lista', compact('veiculos', 'users', 'movimentacoes'));
    }

    /**
     * Cancela (remove) movimentação pelo id.
     */
    public function cancelar($id)
    {
        $deleted = Movimentacao::destroy($id);

        $message = $deleted
            ? 'Movimentação cancelada com sucesso!'
            : 'Movimentação não encontrada ou já removida.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Atualiza uma movimentação.
     * Mantive o comportamento original de resposta JSON quando solicitado.
     */
    public function update(Request $request, $id)
    {

        // cria o validador
        $validator = $this->makeValidator($request->all(), true);
        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'errors' => $validator->errors(),
                    'message' => 'Erro de validação. Verifique os dados enviados.'
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Erro de validação. Verifique os dados informados.')
                ->withInput();
        }

        // encontra movimentação
        $movimentacao = Movimentacao::find($id);
        if (! $movimentacao) {
            $msg = 'Movimentação não encontrada.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $msg], 404);
            }
            return redirect()->back()->with('error', $msg);
        }

        // atualiza km do veículo relacionado (se existir)
        $veiculo = Veiculo::find($movimentacao->veiculo_id);
        if ($veiculo && $request->filled('km_final')) {
            $veiculo->km_atual = $request->km_final;
            $veiculo->save();
        }

        // atualiza movimentação
        $movimentacao->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Movimentação editada com sucesso!',
                'data' => $movimentacao
            ], 200);
        }

        return redirect()->route('dashboard.home')->with('success', 'Movimentação editada com sucesso!');
    }

    /**
     * Inicia e armazena uma nova movimentação.
     */
    public function store(Request $request)
    {
        // validação padrão (redireciona com erros se falhar)
        $validator = $this->makeValidator($request->all());
        if ($validator->fails()) {
            // comportamento similar ao Request::validate original
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Erro de validação. Verifique os dados informados.')
                ->withInput();
        }
        Movimentacao::create($request->all());
        return redirect()->back()->with('success', 'Movimentação Iniciada com sucesso!');
    }

    /**
     * Cria um Validator reutilizável para store/update.
     *
     * @param array $data
     * @param bool $isUpdate exige campos adicionais quando true
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function makeValidator(array $data, bool $isUpdate = false)
    {
        $rules = [
            'veiculo_id' => 'required',
            'user_id' => 'required',
            'tipo_combustivel' => 'required',
            'km_inicial' => 'required',
            'km_rodado' => 'required',
            'origem' => 'required',
            'status' => 'required',
            'destino' => 'sometimes|required',
            'km_final' => 'sometimes|required',
        ];

        // Para update, 'veiculo_id', 'user_id' etc. já estão entre as regras,
        // mas mantemos esta flag caso queira obrigar o 'id' do recurso via request (não usado aqui).
        if ($isUpdate) {
            // se no futuro houver necessidade de exigir 'id' via payload, adicionar aqui:
            // $rules['id'] = 'required';
        }

        $messages = [
            'data.required' => 'O data é obrigatório',
            'hora.required' => 'O hora é obrigatório',
            'veiculo_id.required' => 'O veiculo é obrigatório',
            'user_id.required' => 'O motorista é obrigatório',
            'tipo_combustivel.required' => 'O tipo de combustível é obrigatório',
            'km_inicial.required' => 'O km inicial é obrigatório',
            'km_rodado.required' => 'O km rodado é obrigatório',
            'origem.required' => 'O origem é obrigatório',
            'status.required' => 'O status é obrigatório',
            'destino.required' => 'O destino é obrigatório',
            'km_final.required' => 'O km final é obrigatório',
        ];

        return Validator::make($data, $rules, $messages);
    }
}
