{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_header', 'Visão geral')

@section('content')

    {{-- Gráficos: coloque após os KPI cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-gray-700">KM rodados (últimos 30 dias)</div>
                <div class="text-xs text-gray-400">Total mês: <strong>{{ number_format($kmThisMonth, 1, ',', '.') }} km</strong>
                </div>
            </div>
            <div class="mt-3">
                <canvas id="chartKmPerDay" style="height:260px;"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-gray-700">Movimentações por dia (30 dias)</div>
                <div class="text-xs text-gray-400">Hoje: <strong>{{ $todayMovements }}</strong></div>
            </div>
            <div class="mt-3">
                <canvas id="chartMovPerDay" style="height:260px;"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-3">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-sm font-medium text-gray-700">Distribuição por combustível</div>
            <div class="mt-3">
                <canvas id="chartFuelPie" style="height:220px;"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-4 lg:col-span-2">
            <div class="text-sm font-medium text-gray-700">Movimentações por status</div>
            <div class="mt-3">
                <canvas id="chartStatusBar" style="height:220px;"></canvas>
            </div>
        </div>
    </div>
    <div class="space-y-4">
        {{-- KPI cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-gray-500">Veículos (total)</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $totalVehicles }}</div>
                    </div>
                    <div class="text-sm text-gray-500">Tipos: <strong class="text-gray-700">{{ $tiposCount }}</strong>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500 flex gap-2">
                    <div>Ativos: <strong class="text-green-600">{{ $activeVehicles }}</strong></div>
                    <div class="ml-2">Manutenção: <strong class="text-yellow-600">{{ $inMaintenance }}</strong></div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-gray-500">Motoristas</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $totalDrivers }}</div>
                    </div>
                    <div class="text-sm text-gray-500">Ativos: <strong class="text-green-600">{{ $activeDrivers }}</strong>
                    </div>
                </div>
                <div class="mt-3 text-xs text-gray-500">Gerencie permissões e atualize CNH.</div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs text-gray-500">Movimentações (total)</div>
                        <div class="text-2xl font-semibold text-gray-800">{{ $totalMovements }}</div>
                    </div>
                    <div class="text-sm text-gray-500">Ativas: <strong
                            class="text-yellow-600">{{ $activeMovements }}</strong></div>
                </div>
                <div class="mt-3 text-xs text-gray-500">Hoje: <strong class="text-blue-600">{{ $todayMovements }}</strong>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-xs text-gray-500">KM (resumo)</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format($kmThisMonth, 1, ',', '.') }} km
                </div>
                <div class="mt-2 text-xs text-gray-500">Hoje: <strong
                        class="text-blue-600">{{ number_format($kmToday, 1, ',', '.') }} km</strong></div>
            </div>
        </div>

        {{-- Fuel distribution + movements by status --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">Distribuição por combustível</div>
                    <small class="text-xs text-gray-400">Veículos</small>
                </div>
                <div class="mt-3 space-y-2">
                    @if ($countsByFuel->isEmpty())
                        <div class="text-xs text-gray-500">Nenhum veículo cadastrado.</div>
                    @else
                        @foreach ($countsByFuel as $row)
                            <div class="text-xs text-gray-600 flex items-center justify-between">
                                <div class="capitalize">{{ $row->combustivel ?: 'Outro' }}</div>
                                <div class="font-semibold">{{ $row->total }}</div>
                            </div>
                            <div class="w-full bg-gray-100 rounded h-2 mt-1">
                                @php $pct = $totalVehicles ? round(($row->total / $totalVehicles) * 100) : 0; @endphp
                                <div class="h-2 rounded bg-blue-500" style="width: {{ $pct }}%"></div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm font-medium text-gray-700">Movimentações por status</div>
                    <small class="text-xs text-gray-400">Resumo</small>
                </div>
                <div class="mt-3 space-y-2 text-sm">
                    @foreach ($movByStatus as $status => $count)
                        <div class="flex items-center justify-between">
                            <div class="capitalize text-gray-700">{{ $status }}</div>
                            <div class="font-semibold text-gray-800">{{ $count }}</div>
                        </div>
                    @endforeach
                    @if ($movByStatus->isEmpty())
                        <div class="text-xs text-gray-500">Sem movimentações.</div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-sm font-medium text-gray-700">Veículos com maior KM</div>
                <div class="mt-3 space-y-2">
                    @forelse($topVehiclesByKm as $v)
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <div class="font-medium text-gray-800">{{ $v->placa }} — {{ $v->modelo ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $v->marca ?? '-' }}</div>
                            </div>
                            <div class="text-sm font-semibold">{{ number_format($v->km_atual, 1, ',', '.') }} km</div>
                        </div>
                    @empty
                        <div class="text-xs text-gray-500">Nenhum veículo cadastrado.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent movements table (mobile-friendly cards + desktop table) --}}

    </div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = @json($dates);               // ['2026-02-15', ...]
    const kmPerDay = @json($kmPerDay);         // [12.3, 0, 5.0, ...]
    const movPerDay = @json($movPerDay);       // [1,0,2,...]

    const fuelLabels = @json($fuelLabels);     // ['gasolina','etanol',...]
    const fuelData = @json($fuelData);         // [10,5,2]

    const statusLabels = @json($statusLabels); // ['ativa','finalizada',...]
    const statusData = @json($statusData);     // [3,12,1]

    // helper: responsive config
    const baseOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } }
    };

    // KM line chart
    const ctxKm = document.getElementById('chartKmPerDay').getContext('2d');
    new Chart(ctxKm, {
      type: 'line',
      data: {
        labels: labels.map(d => { const dt = new Date(d); return (dt.getDate().toString().padStart(2,'0')) + '/' + (dt.getMonth()+1).toString().padStart(2,'0'); }),
        datasets: [{
          label: 'KM rodados',
          data: kmPerDay,
          fill: true,
          tension: 0.2,
          borderWidth: 2,
          pointRadius: 2,
          backgroundColor: 'rgba(59,130,246,0.08)',
          borderColor: 'rgba(59,130,246,0.9)',
          pointBackgroundColor: 'rgba(59,130,246,0.9)'
        }]
      },
      options: Object.assign({}, baseOptions, {
        scales: {
          y: { beginAtZero: true, ticks: { callback: v => v + ' km' } },
          x: { ticks: { maxRotation: 0, minRotation: 0 } }
        },
        plugins: {
          tooltip: { callbacks: { label: ctx => ctx.formattedValue + ' km' } }
        }
      })
    });

    // Movements bar chart
    const ctxMov = document.getElementById('chartMovPerDay').getContext('2d');
    new Chart(ctxMov, {
      type: 'bar',
      data: {
        labels: labels.map(d => { const dt = new Date(d); return (dt.getDate().toString().padStart(2,'0')) + '/' + (dt.getMonth()+1).toString().padStart(2,'0'); }),
        datasets: [{
          label: 'Movimentações',
          data: movPerDay,
          borderRadius: 4,
          backgroundColor: 'rgba(16,185,129,0.9)'
        }]
      },
      options: Object.assign({}, baseOptions, {
        scales: { y: { beginAtZero: true, ticks: { precision:0 } } }
      })
    });

    // Fuel pie
    const ctxFuel = document.getElementById('chartFuelPie').getContext('2d');
    new Chart(ctxFuel, {
      type: 'pie',
      data: {
        labels: fuelLabels,
        datasets: [{
          data: fuelData,
          backgroundColor: [
            'rgba(59,130,246,0.9)',
            'rgba(234,88,12,0.9)',
            'rgba(16,185,129,0.9)',
            'rgba(234,179,8,0.9)',
            'rgba(148,163,184,0.9)'
          ]
        }]
      },
      options: Object.assign({}, baseOptions, {
        plugins: { legend: { position: 'right' } }
      })
    });

    // Status horizontal bar
    const ctxStatus = document.getElementById('chartStatusBar').getContext('2d');
    new Chart(ctxStatus, {
      type: 'bar',
      data: {
        labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
        datasets: [{
          label: 'Movimentações',
          data: statusData,
          backgroundColor: statusLabels.map(s => {
            if (s === 'ativa') return 'rgba(234,179,8,0.9)';
            if (s === 'finalizada') return 'rgba(16,185,129,0.9)';
            if (s === 'cancelada') return 'rgba(239,68,68,0.9)';
            return 'rgba(148,163,184,0.9)';
          })
        }]
      },
      options: Object.assign({}, baseOptions, {
        indexAxis: 'y',
        scales: { x: { beginAtZero: true, ticks: { precision:0 } } },
        plugins: { legend: { display: false } }
      })
    });

}); // DOMContentLoaded
</script>
@endpush