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
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $movimentacoes = Movimentacao::with('veiculo', 'user')->paginate(10);
        $veiculos = Veiculo::orderBy('placa')->get();
        $users = User::orderBy('name')->get();
        return view('movimentacao.lista', compact('veiculos', 'users', 'movimentacoes'));
    }

    public function sucesso(Request $request)
    {
        $id = $request->id;
        $movimentacao = Movimentacao::find($id);
        if (!$movimentacao) {
            return redirect()->back()->with('error', 'Movimentação não encontrada.');
        }
        return view('movimentacao.inicioSucesso', compact('movimentacao'));
    }

    public function fim(Request $request)
    {
        $id = $request->id;
        $movimentacao = Movimentacao::find($id);
        return view('movimentacao.fimSucesso', compact('movimentacao'));
    }
    /**
     * Cancela (remove) movimentação pelo id.
     */
    public function cancelar($id, $veiculo_id)
    {

        try {
            $valor = Cache::get('km_inicial_veiculo_' . $veiculo_id);
            if (!$valor) {
                $deleted = Movimentacao::where('id', $id)->update(['status' => 'cancelada']);
                $message = $deleted
                    ? 'Movimentação cancelada com sucesso!'
                    : 'Movimentação não encontrada ou já removida.';
            } else {
                $deleted = Movimentacao::where('id', $id)->update(['status' => 'cancelada']);
                $message = $deleted
                    ? 'Movimentação cancelada com sucesso!'
                    : 'Movimentação não encontrada ou já removida.';
                Veiculo::where('id', $veiculo_id)->update(['km_atual' => $valor]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', $message);
    }

    public function pdf()
    {
        $movimentacoes = Movimentacao::with(['user', 'veiculo'])->where('status', 'finalizada')->where('km_rodado' , '>', 0)
            ->orderByDesc('data')
            ->orderByDesc('hora')
            ->get();

        $totalKm = $movimentacoes->sum(function ($m) {
            return $m->km_rodado ?? (($m->km_final ?? 0) - ($m->km_inicial ?? 0));
        });

        $pdf = PDF::loadView('movimentacao.pdf', compact('movimentacoes', 'totalKm'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('movimentacoes-' . now()->format('Y-m-d_H-i') . '.pdf');
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
                ->with('error', $validator->errors()->first())
                ->withInput();
        }
        // encontra movimentação
        if ($request->km_rodado == '0.0' or $request->km_rodado == '' or $request->km_rodado < 0.0) {
            $msg = 'Você não andou com o veículo. Por favor, verifique os dados informados.';
            return redirect()->back()->with('error', $msg);
        }
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
        return redirect()->route('movimentacao.fim', compact('id'))->with('success', 'Movimentação editada com sucesso!');
    }

    public function destroy($id)
    {
        $movimentacao = Movimentacao::find($id);
        if (!$movimentacao) {
            return redirect()->back()->with('error', 'Movimentação não encontrada.');
        }
        $movimentacao->delete();
        return redirect()->back()->with('success', 'Movimentação removida com sucesso!');
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
        $veiculo_id = $request->veiculo_id;
        $veiculo = Veiculo::find($veiculo_id);
        Cache::put('km_inicial_veiculo_' . $veiculo_id, $veiculo->km_atual, now()->addMinutes(60));
        if (!$veiculo) {
            return redirect()->back()->with('error', 'Veículo não encontrado.');
        }

        try {
            $veiculo->km_atual = $request->km_inicial;
            $veiculo->save();
            $movimentacao = Movimentacao::create($request->all());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('movimentacao.sucesso', ['id' => $movimentacao->id])->with('success', 'Movimentação Iniciada com sucesso!');
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
            'km_rodado.required' => 'A kilometragem inicial ou final está errada',
            'origem.required' => 'O origem é obrigatório',
            'status.required' => 'O status é obrigatório',
            'destino.required' => 'O destino é obrigatório',
            'km_final.required' => 'O km final é obrigatório',
        ];
        return Validator::make($data, $rules, $messages);
    }
}
