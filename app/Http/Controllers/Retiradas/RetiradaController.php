<?php

namespace App\Http\Controllers\Retiradas;

use App\Http\Controllers\Controller;
use App\Models\Cartao;
use App\Models\Ferramenta;
use App\Models\Retirada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RetiradaController extends Controller
{
    private const ADMIN_ROLE = 2;

    private const STATUS_ORDER_SQL = "
        CASE status
            WHEN 'pendente' THEN 1
            WHEN 'pendente entrega' THEN 2
            WHEN 'cancelar entrega' THEN 3
            WHEN 'retirado' THEN 4
            WHEN 'entregue' THEN 5
            WHEN 'cancelado' THEN 6
            WHEN 'negado' THEN 7
            ELSE 8
        END
    ";

public function index(Request $request, ?string $categoria = null)
{
    return $this->list($request, $categoria);
}

public function list(Request $request, ?string $categoria = null)
{
    if ($categoria === 'generica') {
        $categoria = 'generico';
    }

    $cartoes = Cartao::orderBy('nome_veiculo')->get();
    $ferramentas = Ferramenta::orderBy('nome')->get();

    $retiradas = Retirada::with(['user', 'cartao', 'ferramenta'])
        ->where('user_id', Auth::id())
        ->where('status', 'pendente')
        ->latest()
        ->paginate(1)
        ->withQueryString();

    return view('retirada.index', [
        'cartoes' => $cartoes,
        'ferramentas' => $ferramentas,
        'retiradas' => $retiradas,
        'categoriaPadrao' => $categoria,
    ]);
}

    public function cancelar($id)
    {
        $retirada = Retirada::findOrFail($id);
        $this->authorizeOwnerOrAdmin($retirada);

        if (
            !in_array($retirada->status, ['pendente', 'cancelar entrega'], true) ||
            filled($retirada->entrega_autorizada_por)
        ) {
            return back()->with('error', 'Esse pedido já foi autorizado e não pode ser cancelado.');
        }

        $retirada->update(['status' => 'cancelado']);

        return back()->with('success', 'Pedido cancelado com sucesso!');
    }

    public function pedirCancelamentoEntrega($id)
    {
        $retirada = Retirada::findOrFail($id);
        $this->authorizeOwnerOrAdmin($retirada);

        if ($retirada->status !== 'retirado' || filled($retirada->entrega_autorizada_por)) {
            return back()->with('error', 'Esse pedido já foi processado e não pode ser cancelado.');
        }

        $retirada->update(['status' => 'cancelar entrega']);

        return back()->with('success', 'Solicitação de cancelamento enviada com sucesso!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria' => 'required|in:cartao,ferramenta,generico,chaves',
            'cartao_id' => 'nullable|exists:cartoes,id|required_if:categoria,cartao',
            'ferramenta_id' => 'nullable|exists:ferramentas,id|required_if:categoria,ferramenta',
            'nome_generico' => 'nullable|string|max:150|required_if:categoria,generico,chaves',
        ], [
            'categoria.required' => 'A categoria é obrigatória.',
            'categoria.in' => 'Categoria inválida.',
            'cartao_id.required_if' => 'Selecione um cartão.',
            'cartao_id.exists' => 'Cartão inválido.',
            'ferramenta_id.required_if' => 'Selecione uma ferramenta.',
            'ferramenta_id.exists' => 'Ferramenta inválida.',
            'nome_generico.required_if' => 'Digite o nome do item.',
            'nome_generico.string' => 'O nome do item deve ser um texto.',
            'nome_generico.max' => 'O nome do item não pode ter mais de 150 caracteres.',
        ]);

        Retirada::create([
            'user_id' => Auth::id(),
            'categoria' => $validated['categoria'],
            'cartao_id' => $validated['categoria'] === 'cartao' ? $validated['cartao_id'] : null,
            'ferramenta_id' => $validated['categoria'] === 'ferramenta' ? $validated['ferramenta_id'] : null,
            'nome_generico' => in_array($validated['categoria'], ['generico', 'chaves'], true)
                ? $validated['nome_generico']
                : 'Sem nome genérico',
            'status' => 'pendente',
            'retirada_autorizada_por' => null,
            'entrega_autorizada_por' => null,
            'datahora_retirada' => null,
            'datahora_entrega' => null,
        ]);

        return redirect()->route('retirada.list')->with('success', 'Solicitação enviada com sucesso!');
    }

    public function update(Request $request, $id)
    {
        $retirada = Retirada::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pendente,autorizado,negado,retirado,entregue,cancelado,cancelar entrega,pendente entrega',
            'retirada_autorizada_por' => 'nullable|string|max:100',
            'entrega_autorizada_por' => 'nullable|string|max:100',
            'datahora_retirada' => 'nullable|date',
            'datahora_entrega' => 'nullable|date',
        ], [
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'Status inválido.',
        ]);

        $retirada->update($validated);

        return redirect()->route('retirada.list')->with('success', 'Retirada atualizada com sucesso!');
    }

    public function destroy($id)
    {
        Retirada::findOrFail($id)->delete();

        return redirect()->route('retirada.list')->with('success', 'Retirada excluída com sucesso!');
    }

    public function pedirEntrega($id)
    {
        $retirada = Retirada::findOrFail($id);
        $this->authorizeOwnerOrAdmin($retirada);

        $retirada->update(['status' => 'pendente entrega']);

        return back()->with('success', 'Entrega solicitada com sucesso!');
    }

    public function autorizacaoIndex(Request $request)
    {
        abort_unless($this->isAdmin(), 403, 'Acesso restrito ao administrador.');

        $query = Retirada::with(['user', 'cartao', 'ferramenta'])
            ->orderByRaw(self::STATUS_ORDER_SQL);

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        });

        $query->when($request->filled('categoria'), function ($q) use ($request) {
            $q->where('categoria', $request->categoria);
        });

        $retiradas = $query->paginate(
            ($request->filled('status') || $request->filled('categoria')) ? 5 : 10
        )->withQueryString();

        return view('retirada.autorizacao', compact('retiradas'));
    }

    public function concluirEntrega($id)
    {
        $retirada = Retirada::findOrFail($id);
        $this->authorizeOwnerOrAdmin($retirada);

        $retirada->update([
            'status' => 'entregue',
            'entrega_autorizada_por' => $this->userSignature(),
            'datahora_entrega' => now(),
        ]);

        return back()->with('success', 'Entrega concluída com sucesso!');
    }

    public function autorizar(Request $request, $id)
    {
        abort_unless($this->isAdmin(), 403, 'Acesso restrito ao administrador.');

        $validated = $request->validate([
            'status' => 'required|in:retirado,entregue,negado',
        ], [
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'Status inválido.',
        ]);

        $retirada = Retirada::findOrFail($id);

        if (!in_array($retirada->status, ['pendente', 'cancelar entrega'], true)) {
            return back()->with('error', 'Esse pedido já foi processado.');
        }

        $retirada->status = $validated['status'];

        if ($validated['status'] === 'negado') {
            $retirada->retirada_autorizada_por = null;
            $retirada->entrega_autorizada_por = null;
            $retirada->datahora_retirada = null;
            $retirada->datahora_entrega = null;
        } elseif ($validated['status'] === 'entregue') {
            $retirada->entrega_autorizada_por = $this->userSignature();
            $retirada->datahora_entrega = now();
        } else {
            $retirada->retirada_autorizada_por = $this->userSignature();
            $retirada->datahora_retirada = now();
        }

        $retirada->save();

        return back()->with('success', 'Pedido atualizado com sucesso!');
    }

    public function entregaIndex(Request $request)
    {
        $cartoes = Cartao::orderBy('nome_veiculo')->get();
        $ferramentas = Ferramenta::orderBy('nome')->get();

        $query = Retirada::with(['user', 'cartao', 'ferramenta'])
            ->where('user_id', Auth::id())
            ->orderByDesc('id');

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status', $request->status);
        }, function ($q) {
            $q->where('status', '!=', 'cancelado');
        });

        $query->when($request->filled('categoria'), function ($q) use ($request) {
            $q->where('categoria', $request->categoria);
        });

        $retiradas = $query->paginate(1)->withQueryString();

        return view('retirada.entrega', compact('cartoes', 'ferramentas', 'retiradas'));
    }

    private function isAdmin(): bool
    {
        return Auth::check() && (int) Auth::user()->role_id === self::ADMIN_ROLE;
    }

    private function authorizeOwnerOrAdmin(Retirada $retirada): void
    {
        abort_unless(
            $this->isAdmin() || $retirada->user_id === Auth::id(),
            403,
            'Você não tem permissão para executar esta ação.'
        );
    }

    private function userSignature(): string
    {
        $user = Auth::user();

        return trim(($user->name ?? '-') . ' - ' . ($user->cpf ?? '-'));
    }
}
