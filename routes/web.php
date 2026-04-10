<?php

use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\TipoVeiculoController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SecretariaController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    /*
     * Rotas que AMBOS (role 1 e role 2) podem acessar:
     * - criar movimentação
     * - atualizar movimentação
     * - criar usuário (store)
     * - editar usuário (edit) -> controller deve garantir que user comum só edite o próprio
     */
    Route::middleware(['role:1,2'])->group(function () {
        Route::post('/movimentacao/store', [MovimentacaoController::class, 'store'])->name('movimentacao.store');
        Route::post('/movimentacao/update/{id}', [MovimentacaoController::class, 'update'])->name('movimentacao.update');
        Route::get('/user', [UserController::class, 'index'])->name('user.index');
        Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
        Route::get('/movimentacao/veiculo/{veiculoId}', [MovimentacaoController::class, 'withVeiculo'])->name('movimentacao.withVeiculo');
        Route::get('/movimentacao/cancelar/{id}/veiculo/{veiculoId}', [MovimentacaoController::class, 'cancelar'])->name('movimentacao.cancelar');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard.home');
        Route::get('/movimentacao/sucesso', [MovimentacaoController::class, 'sucesso'])->name('movimentacao.sucesso');
        Route::get('/movimentacao/fim', [MovimentacaoController::class, 'fim'])->name('movimentacao.fim');
        Route::post('/user/edit', [UserController::class, 'edit'])->name('user.edit');
        
        });
        
    /*
    * Rotas SOMENTE admin (role = 2)
    * Admin continua tendo acesso a tudo (incluindo as rotas acima).
    */
    Route::middleware(['role:2'])->group(function () {

        Route::post('/movimentacao/cancel/{id}', [MovimentacaoController::class, 'cancelar'])->name('movimentacao.cancelar');
        Route::post('/movimentacao/destroy/{id}', [MovimentacaoController::class, 'destroy'])->name('movimentacao.destroy');
        Route::get('/movimentacoes/pdf', [MovimentacaoController::class, 'pdf'])->name('movimentacao.pdf');
        // Motorista (CRUD)
        Route::get('/movimentacao', [MovimentacaoController::class, 'index'])->name('movimentacao.index');
        // Movimentação (visualização / listagens / cancelamentos por admin)
        Route::get('/movimentacao/list/itens', [MovimentacaoController::class, 'list'])->name('movimentacao.list');

        // Veículos (CRUD)
        Route::get('/veiculo', [VeiculoController::class, 'index'])->name('veiculo.index');
        Route::post('/veiculo/store', [VeiculoController::class, 'store'])->name('veiculo.store');
        Route::get('/veiculo/list', [VeiculoController::class, 'list'])->name('veiculo.list');
        Route::post('/veiculo/destroy/{id}', [VeiculoController::class, 'destroy'])->name('veiculo.destroy');
        Route::post('/veiculo/edit', [VeiculoController::class, 'edit'])->name('veiculo.edit');

        // TipoVeículo (CRUD)
        Route::get('/tipoVeiculo', [TipoVeiculoController::class, 'index'])->name('tipoVeiculo.index');
        Route::post('/tipoVeiculo/store', [TipoVeiculoController::class, 'store'])->name('tipoVeiculo.store');
        Route::get('/tipoVeiculo/list', [TipoVeiculoController::class, 'list'])->name('tipoVeiculo.list');
        Route::post('/tipoVeiculo/destroy/{id}', [TipoVeiculoController::class, 'destroy'])->name('tipoVeiculo.destroy');
        Route::post('/tipoVeiculo/edit', [TipoVeiculoController::class, 'edit'])->name('tipoVeiculo.edit');

        // Usuários (admin management)
        Route::get('/user/list', [UserController::class, 'list'])->name('user.list');
        Route::post('/user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        Route::get('/secretaria', [SecretariaController::class, 'index'])->name('secretaria.index');
        Route::post('/secretaria/store', [SecretariaController::class, 'store'])->name('secretarias.store');
        Route::get('/secretaria/list', [SecretariaController::class, 'list'])->name('secretaria.list');
        Route::post('/secretaria/destroy/{id}', [SecretariaController::class, 'destroy'])->name('secretaria.destroy');
        Route::post('/secretaria/edit', [SecretariaController::class, 'edit'])->name('secretaria.edit');
        // Dashboard (apenas admin por enquanto)
    });
});
