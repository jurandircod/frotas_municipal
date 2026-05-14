<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Secretaria;

class UserController extends Controller
{


    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|max:25',
                'email' => 'required|email',                
                ],
                [
                    'name.required' => 'O nome é obrigatório',
                    'email.required' => 'O email é obrigatório',
                    'cpf.required' => 'O cpf é obrigatório',
                    'telefone.required' => 'O telefone é obrigatório',
                    'cnh_numero.required' => 'O cnh_numero é obrigatório',
                    'cnh_categoria.required' => 'O cnh_categoria é obrigatório',
                    'cnh_validade.required' => 'O cnh_validade é obrigatório',
                    'nascimento.required' => 'O nascimento é obrigatório',
                    'endereco.required' => 'O endereco é obrigatório',
                    'name.max' => 'O nome não pode ter mais de 25 caracteres',
                    'cpf.unique' => 'O cpf já está cadastrado',
            ]
        );
        
        try {
            if ($request->has('id')) {
                $user = User::find($request->id);
                $user->update($request->all());
            } else {
                $user = User::find(Auth::user()->id);
                $user->update($request->all());
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocorreu um erro ao cadastrar o usuário!');
        }

        return redirect()->back()->with('success', 'Usuário cadastrado com sucesso!');
    }

    public function destroy($id)
    {
        User::destroy($id);
        return redirect()->back()->with('success', 'Usuário excluído com sucesso!');
    }


    public function index()
    {
        return view('user.user');
    }

public function list()
{
    $users = User::paginate(10); // 10 usuários por página
    $secretarias = Secretaria::all();

    return view('user.lista', compact('users', 'secretarias'));
}
}
