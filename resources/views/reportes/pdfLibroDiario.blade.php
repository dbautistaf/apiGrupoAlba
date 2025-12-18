<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Libro Diario</title>
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

        .asiento-header {
            background: #e8f4fd;
            padding: 6px;
            margin-top: 8px;
            border: 1px solid #bdd;
            font-weight: bold;
        }

        .cuenta-row {
            background: #f9f9f9;
        }

        .total-row {
            background: #e6f3ff;
            font-weight: bold;
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

        .indent {
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td style="width:80px;">
                <img src="{{ storage_path('app/public/images/ospf.png') }}" alt="logo" class="logo" width="70" />
            </td>
            <td class="title">
                <h2 style="margin:0;color:#28A745">O.S.P.F - LIBRO DIARIO</h2>
                <div class="small">Generado: {{ $reporte['encabezado']['fecha_generacion'] ?? date('Y-m-d H:i:s') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="filters small">
        <tr>
            <td><strong>Período:</strong> {{ $reporte['encabezado']['filtros']['periodo_contable'] ?? '-' }}</td>
            <td><strong>Desde:</strong> {{ $reporte['encabezado']['filtros']['desde'] ?? '-' }}</td>
            <td><strong>Hasta:</strong> {{ $reporte['encabezado']['filtros']['hasta'] ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Número desde:</strong> {{ $reporte['encabezado']['filtros']['numero_desde'] ?? '' }}</td>
            <td><strong>Número hasta:</strong> {{ $reporte['encabezado']['filtros']['numero_hasta'] ?? '' }}</td>
            <td colspan="1"></td>
        </tr>
    </table>

    @foreach($reporte['asientos'] as $asiento)
        <div class="asiento-header">
            {{ $asiento['fecha'] }} - Asiento N° {{ $asiento['numero'] }} - {{ $asiento['leyenda'] }}
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th style="width:400px">Cuenta</th>
                    <th class="right" style="width:100px">Debe</th>
                    <th class="right" style="width:100px">Haber</th>
                    <th class="center" style="width:80px">Recurso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($asiento['cuentas'] as $cuenta)
                    <tr class="cuenta-row">
                        <td class="indent">{{ $cuenta['cuenta'] }}</td>
                        <td class="right">
                            {{ $cuenta['debe'] > 0 ? number_format($cuenta['debe'], 2, ',', '.') : '' }}
                        </td>
                        <td class="right">
                            {{ $cuenta['haber'] > 0 ? number_format($cuenta['haber'], 2, ',', '.') : '' }}
                        </td>
                        <td class="center">{{ $cuenta['recurso'] ?? '' }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td class="right"><strong>Totales del Asiento</strong></td>
                    <td class="right"><strong>{{ number_format($asiento['total_debe'], 2, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($asiento['total_haber'], 2, ',', '.') }}</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <hr />

    <table class="table totals">
        <thead>
            <tr>
                <th class="center">Cantidad de Asientos</th>
                <th class="center">Total Debe</th>
                <th class="center">Total Haber</th>
                <th class="center">Diferencia</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">{{ $reporte['totales']['cantidad_asientos'] }}</td>
                <td class="right">{{ number_format($reporte['totales']['total_debe'], 2, ',', '.') }}</td>
                <td class="right">{{ number_format($reporte['totales']['total_haber'], 2, ',', '.') }}</td>
                <td class="right">{{ number_format($reporte['totales']['diferencia'], 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>