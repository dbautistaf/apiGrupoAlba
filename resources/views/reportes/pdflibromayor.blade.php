<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Libro Mayor</title>
    <style>
        @font-face {
            font-family: 'quicksand';
            src: url('/fonts/Quicksand-Regular.ttf') format('truetype');
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header,
        .filters,
        .totals {
            width: 100%;
            margin-bottom: 8px;
        }

        .header .logo {
            width: 80px;
        }

        .header .title {
            text-align: center;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #333;
            padding: 6px;
            font-size: 11px;
            text-align: left;
        }

        .table th {
            background: #f2f2f2;
            text-align: center;
        }

        .account-header {
            background: #eef6ff;
            padding: 6px;
            margin-top: 8px;
            border: 1px solid #cdd;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td style="width:80px;">
                <img src="{{ storage_path('app/public/images/alba.png') }}" alt="logo" class="logo" width="70" />
            </td>
            <td class="title">
                <h2 style="margin:0;color:#28A745">ALBA - LIBRO MAYOR</h2>
                <div class="small">Generado: {{ $reporte['encabezado']['fecha_generacion'] ?? date('Y-m-d H:i:s') }}
                </div>
            </td>
            {{--<td style="width:200px;">
                @if(!empty($reporte['encabezado']['empresa']))
                <strong>{{ $reporte['encabezado']['empresa']['nombre'] ?? '' }}</strong><br />
                <span class="small">CUIT: {{ $reporte['encabezado']['empresa']['cuit'] ?? '' }}</span>
                @endif
            </td>--}}
        </tr>
    </table>

    <table class="filters small">
        <tr>
            <td><strong>Período:</strong> {{ $reporte['encabezado']['filtros']['id_periodo_contable'] ?? '-' }}</td>
            <td><strong>Desde:</strong> {{ $reporte['encabezado']['filtros']['desde'] ?? '-' }}</td>
            <td><strong>Hasta:</strong> {{ $reporte['encabezado']['filtros']['hasta'] ?? '-' }}</td>
            <td><strong>Incluye saldo anterior:</strong>
                {{ $reporte['encabezado']['filtros']['saldo_anterior'] ?? 'NO' }}</td>
        </tr>
        <tr>
            <td><strong>Código desde:</strong> {{ $reporte['encabezado']['filtros']['codigo_desde'] ?? '' }}</td>
            <td><strong>Código hasta:</strong> {{ $reporte['encabezado']['filtros']['codigo_hasta'] ?? '' }}</td>
            <td colspan="2"></td>
        </tr>
    </table>

    @foreach($reporte['cuentas'] as $cuenta)
        <div class="account-header">
            <table style="width:100%;">
                <tr>
                    <td><strong>{{ $cuenta['codigo_cuenta'] }}</strong> - {{ $cuenta['nombre_cuenta'] }}</td>
                    <td class="right"><strong>Saldo anterior:</strong>
                        {{ number_format($cuenta['saldo_anterior'] ?? 0, 2, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th class="center" style="width:90px">Fecha</th>
                    <th class="center" style="width:70px">Nro</th>
                    <th class="center" style="width:70px">Modelo</th>
                    <th>Concepto / Leyenda</th>
                    <th class="center" style="width:120px">Observaciones</th>
                    <th class="right" style="width:100px">Debe</th>
                    <th class="right" style="width:100px">Haber</th>
                    <th class="right" style="width:110px">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $running = is_array($cuenta['saldo_anterior'] ?? null) ? ($cuenta['saldo_anterior']['saldo_anterior'] ?? 0) : ($cuenta['saldo_anterior'] ?? 0); @endphp
                @if(!empty($cuenta['movimientos']))
                    @foreach($cuenta['movimientos'] as $mov)
                        @php
                            $debe = isset($mov['debe']) ? (float) $mov['debe'] : (float) ($mov['monto_debe'] ?? 0);
                            $haber = isset($mov['haber']) ? (float) $mov['haber'] : (float) ($mov['monto_haber'] ?? 0);
                            $running = $running + $debe - $haber;
                        @endphp
                        <tr>
                            <td class="center">{{ $mov['fecha'] ?? $mov['fecha_asiento'] ?? '' }}</td>
                            <td class="center">{{ $mov['numero'] ?? '' }}</td>
                            <td class="center">{{ $mov['modelo'] ?? $mov['asiento_modelo'] ?? '' }}</td>
                            <td>{{ $mov['leyenda'] ?? $mov['asiento_leyenda'] ?? '' }}</td>
                            <td class="center small">{{ $mov['observaciones'] ?? '' }}</td>
                            <td class="right">{{ number_format($debe, 2, ',', '.') }}</td>
                            <td class="right">{{ number_format($haber, 2, ',', '.') }}</td>
                            <td class="right">{{ number_format($running, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="center small">Sin movimientos en el período</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="5" class="right"><strong>Totales cuenta</strong></td>
                    <td class="right"><strong>{{ number_format($cuenta['total_debe'] ?? 0, 2, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($cuenta['total_haber'] ?? 0, 2, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($cuenta['saldo_final'] ?? $running, 2, ',', '.') }}</strong>
                    </td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <hr />

    <table class="table totals">
        <thead>
            <tr>
                <th class="center">Total Debe</th>
                <th class="center">Total Haber</th>
                <th class="center">Total Saldo Anterior</th>
                <th class="center">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="right">{{ number_format($reporte['totales']['total_debe'] ?? 0, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($reporte['totales']['total_haber'] ?? 0, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($reporte['totales']['total_saldo_anterior'] ?? 0, 2, ',', '.') }}
                </td>
                <td class="right">{{ number_format($reporte['totales']['diferencia'] ?? 0, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>