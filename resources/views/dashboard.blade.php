{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_header', 'Visão geral')

@section('content')

{{-- ===== KPI CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Veículos (total)</div>
                <div class="text-2xl font-semibold text-gray-800">{{ $totalVehicles }}</div>
            </div>
            <div class="text-sm text-gray-500">Tipos: <strong class="text-gray-700">{{ $tiposCount }}</strong></div>
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
            <div class="text-sm text-gray-500">Ativos: <strong class="text-green-600">{{ $activeDrivers }}</strong></div>
        </div>
        <div class="mt-3 text-xs text-gray-500">Gerencie permissões e atualize CNH.</div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-xs text-gray-500">Movimentações (total)</div>
                <div class="text-2xl font-semibold text-gray-800">{{ $totalMovements }}</div>
            </div>
            <div class="text-sm text-gray-500">Ativas: <strong class="text-yellow-600">{{ $activeMovements }}</strong></div>
        </div>
        <div class="mt-3 text-xs text-gray-500">Hoje: <strong class="text-blue-600">{{ $todayMovements }}</strong></div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="text-xs text-gray-500">KM (resumo)</div>
        <div class="mt-1 text-2xl font-semibold text-gray-800">{{ number_format($kmThisMonth, 1, ',', '.') }} km</div>
        <div class="mt-2 text-xs text-gray-500">Hoje: <strong class="text-blue-600">{{ number_format($kmToday, 1, ',', '.') }} km</strong></div>
    </div>
</div>

{{-- ===== LINHA 2: KM por dia + Movimentações por dia ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">KM rodados (últimos 30 dias)</div>
            <div class="text-xs text-gray-400">Total: <strong>{{ number_format($kmThisMonth, 1, ',', '.') }} km</strong></div>
        </div>
        <div style="position:relative; height:240px;"><canvas id="chartKmPerDay"></canvas></div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">Movimentações por dia (30 dias)</div>
            <div class="text-xs text-gray-400">Hoje: <strong>{{ $todayMovements }}</strong></div>
        </div>
        <div style="position:relative; height:240px;"><canvas id="chartMovPerDay"></canvas></div>
    </div>
</div>

{{-- ===== LINHA 3: Combustível + Status + Tipo de veículo ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-3">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Distribuição por combustível</div>
        <div style="position:relative; height:220px;"><canvas id="chartFuelPie"></canvas></div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Movimentações por status</div>
        <div style="position:relative; height:220px;"><canvas id="chartStatusBar"></canvas></div>
    </div>

    {{-- NOVO: Veículos por tipo --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="text-sm font-medium text-gray-700 mb-3">Veículos por tipo</div>
        <div style="position:relative; height:220px;"><canvas id="chartVehicleType"></canvas></div>
    </div>
</div>

{{-- ===== LINHA 4: Top motoristas por KM ===== --}}
<div class="grid grid-cols-1 gap-3 mt-3">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">🏆 Top motoristas — KM rodado (últimos 30 dias)</div>
            <div class="text-xs text-gray-400">Top 8 por quilometragem</div>
        </div>
        <div style="position:relative; height:260px;"><canvas id="chartDriverKm"></canvas></div>
    </div>
</div>

{{-- ===== LINHA 5: Veículos mais usados + KM acumulado por veículo ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
    {{-- NOVO: Top veículos mais utilizados --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">🚗 Veículos mais utilizados (30 dias)</div>
            <div class="text-xs text-gray-400">Por nº de saídas</div>
        </div>
        <div style="position:relative; height:260px;"><canvas id="chartVehicleUsage"></canvas></div>
    </div>

    {{-- NOVO: KM acumulado por veículo (odômetro) --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">📊 KM acumulado por veículo (odômetro)</div>
            <div class="text-xs text-gray-400">Top 6 veículos</div>
        </div>
        <div style="position:relative; height:260px;"><canvas id="chartVehicleKm"></canvas></div>
    </div>
</div>

{{-- ===== LINHA 6: Padrão horário + Padrão semanal ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mt-3">
    {{-- NOVO: Movimentações por hora do dia --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">⏰ Padrão de uso — por hora do dia</div>
            <div class="text-xs text-gray-400">Movimentações acumuladas (30 dias)</div>
        </div>
        <div style="position:relative; height:220px;"><canvas id="chartHour"></canvas></div>
    </div>

    {{-- NOVO: Movimentações por dia da semana --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">📅 Padrão de uso — por dia da semana</div>
            <div class="text-xs text-gray-400">Movimentações acumuladas (30 dias)</div>
        </div>
        <div style="position:relative; height:220px;"><canvas id="chartWeekday"></canvas></div>
    </div>
</div>

{{-- ===== LINHA 7: Listas (combustível + status + top km) ===== --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-3 mt-3">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">Distribuição por combustível</div>
            <small class="text-xs text-gray-400">Veículos</small>
        </div>
        <div class="space-y-2">
            @if ($countsByFuel->isEmpty())
                <div class="text-xs text-gray-500">Nenhum veículo cadastrado.</div>
            @else
                @foreach ($countsByFuel as $row)
                    @php $pct = $totalVehicles ? round(($row->total / $totalVehicles) * 100) : 0; @endphp
                    <div class="text-xs text-gray-600 flex items-center justify-between">
                        <div class="capitalize">{{ $row->combustivel ?: 'Outro' }}</div>
                        <div class="font-semibold">{{ $row->total }}</div>
                    </div>
                    <div class="w-full bg-gray-100 rounded h-2">
                        <div class="h-2 rounded bg-blue-500" style="width: {{ $pct }}%"></div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="text-sm font-medium text-gray-700">Movimentações por status</div>
            <small class="text-xs text-gray-400">Resumo</small>
        </div>
        <div class="space-y-2 text-sm">
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
        <div class="text-sm font-medium text-gray-700 mb-3">Veículos com maior KM (odômetro)</div>
        <div class="space-y-2">
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── dados do PHP ──────────────────────────────────────────────
    const dates      = @json($dates);
    const kmPerDay   = @json($kmPerDay);
    const movPerDay  = @json($movPerDay);

    const fuelLabels   = @json($fuelLabels);
    const fuelData     = @json($fuelData);
    const statusLabels = @json($statusLabels);
    const statusData   = @json($statusData);

    // novos
    const driverKmLabels    = @json($driverKmLabels);
    const driverKmData      = @json($driverKmData);
    const driverMovData     = @json($driverMovData);

    const vehicleUsageLabels   = @json($vehicleUsageLabels);
    const vehicleUsageMovData  = @json($vehicleUsageMovData);
    const vehicleUsageKmData   = @json($vehicleUsageKmData);

    const hourLabels    = @json($hourLabels);
    const hourData      = @json($hourData);
    const weekdayLabels = @json($weekdayLabels);
    const weekdayData   = @json($weekdayData);

    const typeLabels = @json($typeLabels);
    const typeData   = @json($typeData);

    const vehicleKmLabels = @json($vehicleKmLabels);
    const vehicleKmData   = @json($vehicleKmData);

    // ── paleta ────────────────────────────────────────────────────
    const COLORS = [
        'rgba(59,130,246,0.9)',
        'rgba(16,185,129,0.9)',
        'rgba(234,88,12,0.9)',
        'rgba(234,179,8,0.9)',
        'rgba(139,92,246,0.9)',
        'rgba(236,72,153,0.9)',
        'rgba(20,184,166,0.9)',
        'rgba(245,158,11,0.9)',
    ];
    const COLORS_LIGHT = COLORS.map(c => c.replace('0.9)', '0.25)'));

    // ── helper ────────────────────────────────────────────────────
    const fmtDate = d => {
        const dt = new Date(d);
        return String(dt.getDate()).padStart(2,'0') + '/' + String(dt.getMonth()+1).padStart(2,'0');
    };

    const base = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } }
    };

    // ── 1. KM por dia (linha) ─────────────────────────────────────
    new Chart(document.getElementById('chartKmPerDay'), {
        type: 'line',
        data: {
            labels: dates.map(fmtDate),
            datasets: [{
                label: 'KM rodados',
                data: kmPerDay,
                fill: true,
                tension: 0.3,
                borderWidth: 2,
                pointRadius: 2,
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderColor: 'rgba(59,130,246,0.9)',
                pointBackgroundColor: 'rgba(59,130,246,0.9)'
            }]
        },
        options: {
            ...base,
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => v + ' km' } },
                x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } }
            },
            plugins: { ...base.plugins, tooltip: { callbacks: { label: c => c.formattedValue + ' km' } } }
        }
    });

    // ── 2. Movimentações por dia (barras) ─────────────────────────
    new Chart(document.getElementById('chartMovPerDay'), {
        type: 'bar',
        data: {
            labels: dates.map(fmtDate),
            datasets: [{
                label: 'Movimentações',
                data: movPerDay,
                borderRadius: 4,
                backgroundColor: 'rgba(16,185,129,0.85)'
            }]
        },
        options: {
            ...base,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 10 } }
            }
        }
    });

    // ── 3. Combustível (pie) ──────────────────────────────────────
    new Chart(document.getElementById('chartFuelPie'), {
        type: 'pie',
        data: {
            labels: fuelLabels,
            datasets: [{ data: fuelData, backgroundColor: COLORS }]
        },
        options: { ...base, plugins: { ...base.plugins, legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } }
    });

    // ── 4. Status (barra horizontal) ──────────────────────────────
    new Chart(document.getElementById('chartStatusBar'), {
        type: 'bar',
        data: {
            labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
            datasets: [{
                label: 'Movimentações',
                data: statusData,
                backgroundColor: statusLabels.map(s => {
                    if (s === 'ativa')     return 'rgba(234,179,8,0.9)';
                    if (s === 'finalizada') return 'rgba(16,185,129,0.9)';
                    if (s === 'cancelada') return 'rgba(239,68,68,0.9)';
                    return 'rgba(148,163,184,0.9)';
                })
            }]
        },
        options: {
            ...base,
            indexAxis: 'y',
            scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            plugins: { ...base.plugins, legend: { display: false } }
        }
    });

    // ── 5. NOVO: Veículos por tipo (doughnut) ─────────────────────
    new Chart(document.getElementById('chartVehicleType'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeData,
                backgroundColor: COLORS,
                borderWidth: 2
            }]
        },
        options: {
            ...base,
            cutout: '60%',
            plugins: {
                ...base.plugins,
                legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } }
            }
        }
    });

    // ── 6. NOVO: Top motoristas por KM (barras horizontais duplas) ─
    new Chart(document.getElementById('chartDriverKm'), {
        type: 'bar',
        data: {
            labels: driverKmLabels,
            datasets: [
                {
                    label: 'KM rodado',
                    data: driverKmData,
                    backgroundColor: 'rgba(59,130,246,0.85)',
                    borderRadius: 4,
                    yAxisID: 'yKm'
                },
                {
                    label: 'Nº de saídas',
                    data: driverMovData,
                    backgroundColor: 'rgba(16,185,129,0.75)',
                    borderRadius: 4,
                    type: 'line',
                    tension: 0.3,
                    borderColor: 'rgba(16,185,129,1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: false,
                    yAxisID: 'yMov'
                }
            ]
        },
        options: {
            ...base,
            indexAxis: 'y',
            scales: {
                yKm:  { type: 'linear', position: 'bottom', beginAtZero: true, ticks: { callback: v => v + ' km' } },
                yMov: { type: 'linear', position: 'top',    beginAtZero: true, ticks: { precision: 0 }, grid: { drawOnChartArea: false } },
                y: { ticks: { font: { size: 11 } } }
            },
            plugins: {
                ...base.plugins,
                legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label === 'KM rodado'
                            ? ctx.formattedValue + ' km'
                            : ctx.formattedValue + ' saídas'
                    }
                }
            }
        }
    });

    // ── 7. NOVO: Veículos mais utilizados (barras + linha KM) ──────
    new Chart(document.getElementById('chartVehicleUsage'), {
        type: 'bar',
        data: {
            labels: vehicleUsageLabels,
            datasets: [
                {
                    label: 'Saídas',
                    data: vehicleUsageMovData,
                    backgroundColor: 'rgba(139,92,246,0.85)',
                    borderRadius: 4,
                    yAxisID: 'yMov'
                },
                {
                    label: 'KM rodado',
                    data: vehicleUsageKmData,
                    type: 'line',
                    tension: 0.3,
                    borderColor: 'rgba(234,88,12,0.9)',
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(234,88,12,0.9)',
                    fill: false,
                    yAxisID: 'yKm'
                }
            ]
        },
        options: {
            ...base,
            scales: {
                yMov: { type: 'linear', position: 'left',  beginAtZero: true, ticks: { precision: 0 }, title: { display: true, text: 'Saídas' } },
                yKm:  { type: 'linear', position: 'right', beginAtZero: true, ticks: { callback: v => v + ' km' }, grid: { drawOnChartArea: false }, title: { display: true, text: 'KM' } },
                x: { ticks: { font: { size: 10 }, maxRotation: 30 } }
            },
            plugins: {
                ...base.plugins,
                legend: { position: 'top', labels: { boxWidth: 12, font: { size: 11 } } }
            }
        }
    });

    // ── 8. NOVO: KM acumulado por veículo (radar) ─────────────────
    new Chart(document.getElementById('chartVehicleKm'), {
        type: 'bar',
        data: {
            labels: vehicleKmLabels,
            datasets: [{
                label: 'KM acumulado (odômetro)',
                data: vehicleKmData,
                backgroundColor: vehicleKmLabels.map((_, i) => COLORS[i % COLORS.length]),
                borderRadius: 6,
                borderWidth: 0
            }]
        },
        options: {
            ...base,
            indexAxis: 'y',
            scales: {
                x: { beginAtZero: true, ticks: { callback: v => Number(v).toLocaleString('pt-BR') + ' km' } },
                y: { ticks: { font: { size: 11 } } }
            },
            plugins: {
                ...base.plugins,
                legend: { display: false },
                tooltip: { callbacks: { label: c => Number(c.raw).toLocaleString('pt-BR') + ' km' } }
            }
        }
    });

    // ── 9. NOVO: Padrão por hora do dia ───────────────────────────
    new Chart(document.getElementById('chartHour'), {
        type: 'bar',
        data: {
            labels: hourLabels,
            datasets: [{
                label: 'Movimentações',
                data: hourData,
                backgroundColor: hourData.map(v => {
                    const max = Math.max(...hourData) || 1;
                    const alpha = 0.3 + 0.65 * (v / max);
                    return `rgba(59,130,246,${alpha.toFixed(2)})`;
                }),
                borderRadius: 3
            }]
        },
        options: {
            ...base,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
                x: { ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }
            },
            plugins: { ...base.plugins, legend: { display: false } }
        }
    });

    // ── 10. NOVO: Padrão por dia da semana ────────────────────────
    new Chart(document.getElementById('chartWeekday'), {
        type: 'bar',
        data: {
            labels: weekdayLabels,
            datasets: [{
                label: 'Movimentações',
                data: weekdayData,
                backgroundColor: [
                    'rgba(148,163,184,0.8)', // Dom
                    'rgba(59,130,246,0.85)', // Seg
                    'rgba(59,130,246,0.85)', // Ter
                    'rgba(59,130,246,0.85)', // Qua
                    'rgba(59,130,246,0.85)', // Qui
                    'rgba(59,130,246,0.85)', // Sex
                    'rgba(148,163,184,0.8)', // Sáb
                ],
                borderRadius: 5
            }]
        },
        options: {
            ...base,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: { ...base.plugins, legend: { display: false } }
        }
    });

}); // DOMContentLoaded
</script>
@endpush