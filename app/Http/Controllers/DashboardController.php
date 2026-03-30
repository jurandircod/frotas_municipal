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

        // KPI básicos
        $totalVehicles  = Veiculo::count();
        $activeVehicles = Veiculo::where('status', 'ativo')->count();
        $inMaintenance  = Veiculo::where('status', 'manutencao')->count();
        $inactiveVehicles = Veiculo::where('status', 'inativo')->count();

        $totalDrivers  = User::count();
        $activeDrivers = User::where('status', 'ativo')->count();

        $totalMovements  = Movimentacao::count();
        $activeMovements = Movimentacao::where('status', 'ativa')->count();
        $todayMovements  = Movimentacao::whereDate('data', $today)->count();

        // expressão de km
        $kmExpression = "COALESCE(movimentacoes.km_rodado, (movimentacoes.km_final - movimentacoes.km_inicial), 0)";

        $kmToday = Movimentacao::whereDate('data', $today)
            ->select(DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->value('total_km') ?? 0;

        $kmThisMonth = Movimentacao::whereDate('data', '>=', $today->copy()->startOfMonth())
            ->select(DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->value('total_km') ?? 0;

        // recentes e top vehicles
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

        // --- Dados para gráficos existentes (últimos $days dias) ---
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $dates[] = $d->format('Y-m-d');
        }

        $kmPerDayRaw = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->select('data', DB::raw("IFNULL(SUM($kmExpression),0) as total_km"))
            ->groupBy('data')
            ->pluck('total_km', 'data')
            ->toArray();

        $kmPerDay = [];
        foreach ($dates as $d) {
            $kmPerDay[] = isset($kmPerDayRaw[$d]) ? (float) $kmPerDayRaw[$d] : 0.0;
        }

        $movPerDayRaw = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->select('data', DB::raw('COUNT(*) as total'))
            ->groupBy('data')
            ->pluck('total', 'data')
            ->toArray();

        $movPerDay = [];
        foreach ($dates as $d) {
            $movPerDay[] = isset($movPerDayRaw[$d]) ? (int) $movPerDayRaw[$d] : 0;
        }

        $fuelLabels = $countsByFuel->pluck('combustivel')->map(fn($v) => $v ?: 'Outro')->toArray();
        $fuelData   = $countsByFuel->pluck('total')->map(fn($v) => (int)$v)->toArray();

        $statusLabels = $movByStatus->keys()->toArray();
        $statusData   = $movByStatus->values()->map(fn($v) => (int)$v)->toArray();

        $tiposCount = TipoVeiculo::count();

        // =========================================================
        // NOVOS GRÁFICOS
        // =========================================================

        // 1. Top motoristas por KM rodado (últimos 30 dias)
        $topDriversByKm = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->join('users', 'movimentacoes.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw("IFNULL(SUM($kmExpression), 0) as total_km"),
                DB::raw('COUNT(movimentacoes.id) as total_mov')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_km')
            ->limit(8)
            ->get();

        $driverKmLabels = $topDriversByKm->pluck('name')
            ->map(fn($n) => mb_strlen($n) > 20 ? mb_substr($n, 0, 18) . '…' : $n)
            ->toArray();
        $driverKmData   = $topDriversByKm->pluck('total_km')->map(fn($v) => round((float)$v, 1))->toArray();
        $driverMovData  = $topDriversByKm->pluck('total_mov')->map(fn($v) => (int)$v)->toArray();

        // 2. Top veículos mais utilizados (por nº de movimentações, últimos 30 dias)
        $topVehiclesByUsage = Movimentacao::whereBetween('data', [$start->format('Y-m-d'), $today->format('Y-m-d')])
            ->join('veiculos', 'movimentacoes.veiculo_id', '=', 'veiculos.id')
            ->select(
                'veiculos.placa',
                DB::raw("IFNULL(veiculos.modelo, veiculos.placa) as label"),
                DB::raw('COUNT(movimentacoes.id) as total_mov'),
                DB::raw("IFNULL(SUM($kmExpression), 0) as total_km")
            )
            ->groupBy('veiculos.id', 'veiculos.placa', 'veiculos.modelo')
            ->orderByDesc('total_mov')
            ->limit(8)
            ->get();

        $vehicleUsageLabels = $topVehiclesByUsage->map(fn($v) => $v->placa . ' ' . mb_substr($v->label ?? '', 0, 10))->toArray();
        $vehicleUsageMovData = $topVehiclesByUsage->pluck('total_mov')->map(fn($v) => (int)$v)->toArray();
        $vehicleUsageKmData  = $topVehiclesByUsage->pluck('total_km')->map(fn($v) => round((float)$v, 1))->toArray();

        // 3. Movimentações por hora do dia (padrão de uso)
        $movByHour = Movimentacao::whereDate('data', '>=', $start->format('Y-m-d'))
            ->whereNotNull('hora')
            ->select(DB::raw('HOUR(hora) as hour'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('HOUR(hora)'))
            ->pluck('total', 'hour')
            ->toArray();

        $hourLabels = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . 'h', range(0, 23));
        $hourData   = array_map(fn($h) => isset($movByHour[$h]) ? (int)$movByHour[$h] : 0, range(0, 23));

        // 4. Movimentações por dia da semana (seg–dom)
        $movByWeekday = Movimentacao::whereDate('data', '>=', $start->format('Y-m-d'))
            ->select(DB::raw('DAYOFWEEK(data) as dow'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('DAYOFWEEK(data)'))
            ->pluck('total', 'dow')
            ->toArray();
        // DAYOFWEEK: 1=Dom, 2=Seg ... 7=Sáb
        $weekdayLabels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $weekdayData   = [];
        for ($dow = 1; $dow <= 7; $dow++) {
            $weekdayData[] = isset($movByWeekday[$dow]) ? (int)$movByWeekday[$dow] : 0;
        }

        // 5. Veículos por tipo (para gráfico doughnut)
        $vehiclesByType = TipoVeiculo::withCount('veiculos')
            ->orderByDesc('veiculos_count')
            ->get();
        $typeLabels = $vehiclesByType->pluck('nome')->map(fn($v) => $v ?: 'Sem tipo')->toArray();
        $typeData   = $vehiclesByType->pluck('veiculos_count')->map(fn($v) => (int)$v)->toArray();

        // 6. KM acumulado por veículo (top 6, radar / bar horizontal)
        $vehicleKmLabels = $topVehiclesByKm->map(fn($v) => $v->placa)->toArray();
        $vehicleKmData   = $topVehiclesByKm->map(fn($v) => round((float)$v->km_atual, 1))->toArray();

        return view('dashboard', compact(
            // existentes
            'totalVehicles', 'activeVehicles', 'inMaintenance', 'inactiveVehicles',
            'totalDrivers', 'activeDrivers',
            'totalMovements', 'activeMovements', 'todayMovements',
            'kmToday', 'kmThisMonth',
            'recentMovements', 'topVehiclesByKm',
            'countsByFuel', 'movByStatus', 'tiposCount',
            'dates', 'kmPerDay', 'movPerDay',
            'fuelLabels', 'fuelData',
            'statusLabels', 'statusData',
            // novos
            'driverKmLabels', 'driverKmData', 'driverMovData',
            'vehicleUsageLabels', 'vehicleUsageMovData', 'vehicleUsageKmData',
            'hourLabels', 'hourData',
            'weekdayLabels', 'weekdayData',
            'typeLabels', 'typeData',
            'vehicleKmLabels', 'vehicleKmData'
        ));
    }
}