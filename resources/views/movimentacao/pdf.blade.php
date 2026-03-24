<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Movimentações</title>
    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1d4ed8;
            margin: 0;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        .summary {
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }

        .summary td {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            vertical-align: top;
        }

        .summary strong {
            display: block;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .summary span {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.data th {
            background: #1d4ed8;
            color: #ffffff;
            padding: 8px 6px;
            font-size: 10px;
            text-align: left;
            border: 1px solid #1e40af;
        }

        table.data td {
            border: 1px solid #e5e7eb;
            padding: 7px 6px;
            vertical-align: top;
            word-wrap: break-word;
            font-size: 10px;
        }

        table.data tr:nth-child(even) td {
            background: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .muted {
            color: #6b7280;
        }

        .footer {
            margin-top: 14px;
            font-size: 9px;
            color: #6b7280;
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            background: #e0f2fe;
            color: #075985;
            font-size: 9px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Relatório de Movimentações</h1>
        <div class="subtitle">
            Emitido em: {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <strong>Total de corridas</strong>
                <span>{{ $movimentacoes->count() }}</span>
            </td>
            <td>
                <strong>KM total rodado</strong>
                <span>{{ number_format($totalKm, 1, ',', '.') }} km</span>
            </td>
            <td>
                <strong>Período</strong>
                <span>
                    @if($movimentacoes->count())
                        {{ \Carbon\Carbon::parse($movimentacoes->last()->data)->format('d/m/Y') }}
                        até
                        {{ \Carbon\Carbon::parse($movimentacoes->first()->data)->format('d/m/Y') }}
                    @else
                        Sem registros
                    @endif
                </span>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">Data Início</th>
                <th style="width: 8%;">Hora Início</th>
                <th style="width: 8%;">Data Fim</th>
                <th style="width: 8%;">Hora Fim</th>
                <th style="width: 13%;">Motorista</th>
                <th style="width: 12%;">Veículo</th>
                <th style="width: 11%;">Origem</th>
                <th style="width: 11%;">Destino</th>
                <th style="width: 8%;">KM Inicial</th>
                <th style="width: 8%;">KM Final</th>
                <th style="width: 8%;">KM Rodado</th>
                <th style="width: 7%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimentacoes as $m)
                <tr>
                    <td>
                        {{ $m->data ? \Carbon\Carbon::parse($m->data)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ substr($m->hora ?? '-', 0, 5) }}</td>
                    <td>
                        {{ $m->data_fim ? \Carbon\Carbon::parse($m->data_fim)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ substr($m->hora_fim ?? '-', 0, 5) }}</td>
                    <td>{{ $m->user->name ?? ($m->motorista_nome ?? '-') }}</td>
                    <td>
                        {{ $m->veiculo_placa ?? ($m->veiculo->placa ?? '-') }}
                        @if($m->veiculo && $m->veiculo->modelo)
                            <br><span class="muted">{{ $m->veiculo->modelo }}</span>
                        @endif
                    </td>
                    <td>{{ $m->origem ?? '-' }}</td>
                    <td>{{ $m->destino ?? '-' }}</td>
                    <td class="text-right">
                        {{ isset($m->km_inicial) ? number_format($m->km_inicial, 1, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ isset($m->km_final) ? number_format($m->km_final, 1, ',', '.') : '-' }}
                    </td>
                    <td class="text-right">
                        {{ number_format($m->km_rodado ?? (($m->km_final ?? 0) - ($m->km_inicial ?? 0)), 1, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge">{{ ucfirst($m->status ?? 'ativa') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="12">
                        <strong>Observações:</strong>
                        {{ $m->observacao ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center muted">Nenhuma movimentação encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Relatório gerado automaticamente pelo sistema.
    </div>
</body>
</html>