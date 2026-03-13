<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
/**$totVeiculos = Vehicle::count();
$veiculosAtivos = Vehicle::where('status','ativo')->count();
$totMotoristas = Driver::count();
$motoristasValidos = Driver::whereDate('cnh_validade','>=', now())->count();
$movimentacoesHoje = Movement::whereDate('data', now()->toDateString())->count();
$recentMovimentacoes = Movement::with('veiculo','motorista')->latest()->take(8)->get();
$recentVeiculos = Vehicle::latest()->take(6)->get();

// Dados para gráfico (últimos 7 dias)
$chartLabels = []; $chartData = [];
for ($i = 6; $i >= 0; $i--) {
  $date = now()->subDays($i);
  $chartLabels[] = $date->format('d/m');
  $chartData[] = Movement::whereDate('data', $date->toDateString())->sum(DB::raw('km_final - km_inicial'));
}

return view('dashboard', compact(
  'totVeiculos','veiculosAtivos','totMotoristas','motoristasValidos',
  'movimentacoesHoje','recentMovimentacoes','recentVeiculos',
  'chartLabels','chartData','kmRodadoHoje','movimentacoesAtivas','movimentacoesFinalizadas','kmMediaPorVeiculo'
));
 */

}
