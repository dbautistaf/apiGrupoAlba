<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>eCheq pendientes de emisión</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            color: #334155;
            font-size: 10px;
            background-color: #ffffff;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 3px 5px; }

        .header-container {
            background-color: #f8fafc;
            border-bottom: 3px solid #388E3C;
            padding: 8px 15px;
            margin-bottom: 10px;
        }

        .titulo { font-size: 15px; font-weight: bold; color: #0f172a; }
        .subtitulo { color: #64748b; font-size: 9px; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }

        .contenido { padding: 0 15px; }

        .banco-titulo {
            background-color: #388E3C;
            color: #ffffff;
            font-weight: bold;
            padding: 4px 6px;
            margin-top: 12px;
            font-size: 11px;
        }

        .tabla-datos th {
            background-color: #f1f5f9;
            border-bottom: 1px solid #cbd5e1;
            font-size: 9px;
            text-transform: uppercase;
            color: #475569;
        }

        .tabla-datos td { border-bottom: 1px solid #e2e8f0; }

        /* La columna del número va vacía a propósito: se completa a mano con lo que
           devuelve el banco al emitir. */
        .celda-echeq {
            border-bottom: 1px solid #94a3b8 !important;
            min-width: 90px;
        }

        .subtotal td {
            border-top: 1px solid #94a3b8;
            font-weight: bold;
            background-color: #f8fafc;
        }

        .total-general {
            margin-top: 14px;
            border-top: 2px solid #388E3C;
            padding-top: 6px;
            font-size: 11px;
            font-weight: bold;
        }

        .vacio { padding: 20px; text-align: center; color: #64748b; }
    </style>
</head>

<body>
    <div class="header-container">
        <table>
            <tr>
                <td>
                    <div class="titulo">eCheq pendientes de emisión</div>
                    <div class="subtitulo">Órdenes de pago vigentes con pagos sin número asignado</div>
                </td>
                <td class="text-right subtitulo">
                    Emitido: {{ $fecha_emision }}<br />
                    {{ $total_registros }} pago(s)
                </td>
            </tr>
        </table>
    </div>

    <div class="contenido">
        @forelse ($grupos as $nombreBanco => $pagos)
            <div class="banco-titulo">{{ $nombreBanco }}</div>

            <table class="tabla-datos">
                <thead>
                    <tr>
                        <th style="width: 15%">N° Orden de Pago</th>
                        <th style="width: 37%">Proveedor / Prestador</th>
                        <th style="width: 20%">N° eCheq</th>
                        <th style="width: 14%" class="text-right">Monto</th>
                        <th style="width: 14%" class="text-center">Fecha de pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pagos as $p)
                        <tr>
                            <td>{{ $p['num_opa'] }}</td>
                            <td>{{ $p['beneficiario'] }}</td>
                            <td class="celda-echeq">{{ $p['numero_echeq'] }}</td>
                            <td class="text-right">{{ $p['monto'] }}</td>
                            <td class="text-center">{{ $p['fecha_pago'] }}</td>
                        </tr>
                    @endforeach
                    <tr class="subtotal">
                        <td colspan="3" class="text-right">Subtotal {{ $nombreBanco }}</td>
                        <td class="text-right">{{ $subtotales[$nombreBanco] }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @empty
            <div class="vacio">No hay eCheq pendientes de emisión.</div>
        @endforelse

        @if ($total_registros > 0)
            <table class="total-general">
                <tr>
                    <td class="text-right">TOTAL GENERAL</td>
                    <td class="text-right" style="width: 14%">{{ $total_general }}</td>
                    <td style="width: 14%"></td>
                </tr>
            </table>
        @endif
    </div>
</body>

</html>
