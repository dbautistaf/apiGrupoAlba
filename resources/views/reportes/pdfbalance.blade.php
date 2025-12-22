<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Balance de Saldos</title>
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
                <h2 style="margin:0;color:#28A745">O.S.P.F - BALANCE DE SALDOS</h2>
                <div class="small">Generado: {{ $reporte['encabezado']['fecha_generacion'] ?? date('Y-m-d H:i:s') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="filters small">
        <tr>
            <td><strong>Período:</strong> {{ $reporte['encabezado']['filtros']['anio_periodo'] ?? '-' }}</td>
            <td><strong>Desde:</strong> {{ $reporte['encabezado']['filtros']['desde'] ?? '-' }}</td>
            <td><strong>Hasta:</strong> {{ $reporte['encabezado']['filtros']['hasta'] ?? '-' }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th class="center">Código</th>
                <th>Cuenta</th>
                <th class="right">Saldo Anterior</th>
                <th class="right">Debe</th>
                <th class="right">Haber</th>
                <th class="right">Saldo Final</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporte['cuentas'] as $cuenta)
                <tr>
                    <td class="center">{{ $cuenta['codigo_cuenta'] }}</td>
                    <td>{{ $cuenta['nombre_cuenta'] }}</td>
                    <td class="right">{{ number_format($cuenta['saldo_anterior'] ?? 0, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($cuenta['total_debe'] ?? 0, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($cuenta['total_haber'] ?? 0, 2, ',', '.') }}</td>
                    <td class="right">{{ number_format($cuenta['saldo_final'] ?? 0, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

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