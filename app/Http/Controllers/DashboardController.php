<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\Movimentacao;
use App\Models\TipoVeiculo;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $days = 30;
        $start = $today->copy()->subDays($days - 1);

        // KPI básicos (como antes)
        $totalVehicles = Veiculo::count();
        $activeVehicles = Veiculo::where('status', 'ativo')->count();
        $inMaintenance = Veiculo::where('status', 'manutencao')->count();
        $inactiveVehicles = Veiculo::where('status', 'inativo')->count();

        $totalDrivers = User::count();
        $activeDrivers = User::where('status', 'ativo')->count();

        $totalMovements = Movimentacao::count();
        $activeMovements = Movimentacao::where('status', 'ativa')->count();
        $todayMovements = Movimentacao::whereDate('data', $today)->count();

        // expressão de km (usa km_rodado se existir)
        $kmExpression = "COALESCE(movimentacoes.km_rodado, (movimentacoes.km_final - movimentacoes.km_inicial), 0)";

        $kmToday = Movimentacao::whereDate('data', $today)
            ->select(DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->value('total_km') ?? 0;

        $kmThisMonth = Movimentacao::whereDate('data', '>=', $today->copy()->startOfMonth())
            ->select(DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->value('total_km') ?? 0;

        // recentes e top vehicles (como antes)
        $recentMovements = Movimentacao::with(['veiculo', 'user'])
            ->orderBy('data', 'desc')
            ->orderBy('hora', 'desc')
            ->limit(8)
            ->get();

        $topVehiclesByKm = Veiculo::orderBy('km_atual', 'desc')->limit(6)->get();

        // contagem por combustível
        $countsByFuel = Veiculo::select('combustivel', DB::raw('COUNT(*) as total'))
            ->groupBy('combustivel')
            ->get();

        // mov por status
        $movByStatus = Movimentacao::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // --- Dados para gráficos (últimos $days dias) ---
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $dates[] = $d->format('Y-m-d');
        }

        // km por dia (soma)
        $kmPerDayRaw = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->select('data', DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->groupBy('data')
            ->pluck('total_km', 'data')
            ->toArray();

        $kmPerDay = [];
        foreach ($dates as $d) {
            $kmPerDay[] = isset($kmPerDayRaw[$d]) ? (float) $kmPerDayRaw[$d] : 0.0;
        }

        // movimentos por dia (contagem)
        $movPerDayRaw = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->select('data', DB::raw('COUNT(*) as total'))
            ->groupBy('data')
            ->pluck('total', 'data')
            ->toArray();

        $movPerDay = [];
        foreach ($dates as $d) {
            $movPerDay[] = isset($movPerDayRaw[$d]) ? (int) $movPerDayRaw[$d] : 0;
        }

        // preparar labels / dados de combustível para pie
        $fuelLabels = $countsByFuel->pluck('combustivel')->map(function($v){ return $v ?: 'Outro'; })->toArray();
        $fuelData = $countsByFuel->pluck('total')->map(fn($v) => (int)$v)->toArray();

        // status labels/data
        $statusLabels = $movByStatus->keys()->toArray();
        $statusData = $movByStatus->values()->map(fn($v) => (int)$v)->toArray();

        $tiposCount = TipoVeiculo::count();

        return view('dashboard', compact(
            'totalVehicles','activeVehicles','inMaintenance','inactiveVehicles',
            'totalDrivers','activeDrivers',
            'totalMovements','activeMovements','todayMovements',
            'kmToday','kmThisMonth',
            'recentMovements','topVehiclesByKm',
            'countsByFuel','movByStatus','tiposCount',
            // charts
            'dates','kmPerDay','movPerDay','fuelLabels','fuelData','statusLabels','statusData'
        ));
    }
}