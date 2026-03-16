<?php

use App\Http\Controllers\MotoristaController;
use App\Http\Controllers\MovimentacaoController;
use App\Http\Controllers\TipoVeiculoController;
use App\Http\Controllers\VeiculoController;
use App\Models\TipoVeiculo;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Enums\Role;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

require __DIR__ . '/auth.php';
Route::middleware(['auth'])->group(function () {

    Route::get('/movimentacao', [MovimentacaoController::class, 'index'])->name('movimentacao.index');
    Route::post('/movimentacao/store', [MovimentacaoController::class, 'store'])->name('movimentacao.store');
    Route::post('/movimentacao/update/{id}', [MovimentacaoController::class, 'update'])->name('movimentacao.update');
    Route::get('/movimentacao/veiculo/{veiculoId}', [MovimentacaoController::class, 'withVeiculo'])->name('movimentacao.index');
    Route::get('/movimentacao/list/itens', [MovimentacaoController::class, 'list'])->name('movimentacao.list');
    Route::post('/movimentacao/destroy/{id}', [MovimentacaoController::class, 'cancelar'])->name('movimentacao.destroy');
    Route::get('/movimentacao/cancelar/{id}', [MovimentacaoController::class, 'cancelar'])->name('movimentacao.cancelar');


    Route::get('/veiculo', [VeiculoController::class, 'index'])->name('veiculo.index');
    Route::post('/veiculo/store', [VeiculoController::class, 'store'])->name('veiculo.store');
    Route::get('/veiculo/list', [VeiculoController::class, 'list'])->name('veiculo.list');
    Route::post('/veiculo/destroy/{id}', [VeiculoController::class, 'destroy'])->name('veiculo.destroy');
    Route::post('/veiculo/edit', [VeiculoController::class, 'edit'])->name('veiculo.edit');


    Route::get('/tipoVeiculo', [TipoVeiculoController::class, 'index'])->name('tipoVeiculo.index');
    Route::post('/tipoVeiculo/store', [TipoVeiculoController::class, 'store'])->name('tipoVeiculo.store');
    Route::get('/tipoVeiculo/list', [TipoVeiculoController::class, 'list'])->name('tipoVeiculo.list');
    Route::post('/tipoVeiculo/destroy/{id}', [TipoVeiculoController::class, 'destroy'])->name('tipoVeiculo.destroy');
    Route::post('/tipoVeiculo/edit', [TipoVeiculoController::class, 'edit'])->name('tipoVeiculo.edit');
    
    
    Route::get('/user', [userController::class, 'index'])->name('user.index');
    Route::get('/user/list', [UserController::class, 'list'])->name('user.list');
    Route::post('/user/destroy/{id}', [userController::class, 'destroy'])->name('user.destroy');
    Route::post('/user/edit', [userController::class, 'edit'])->name('user.edit');
    Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
});







Route::get('/dashboard', function () {
        Route::get('/movimentacao/list/itens', [MovimentacaoController::class, 'list'])->name('movimentacao.list');
})->name('dashboard');
