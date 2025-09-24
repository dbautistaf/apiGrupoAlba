<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Debito Liquidación</title>
    <style>
        * {
            margin: 4px;
            box-sizing: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
        }

        .img,
        .numero {
            position: absolute;
        }

        .numero {
            margin-top: 1cm;
            margin-left: 22cm;
        }

        .numero span {
            padding: 8px;
            background: #e9e7e7;
            border-radius: 8px;
        }

        .img img {
            position: relative;
            max-width: 1000px;
            max-height: 700px;
        }


        .datos {
            margin-top: 0.1cm;

        }

        .datos p {
            padding: 10px;
        }

        .datos span {
            padding: 8px;
            background: #e9e7e7;
            border-radius: 8px;
        }

        .fecha {
            margin-left: 2.8cm;
        }
        .datos .prestador{
            line-height: 2;
        }

        .detalle {
            margin-top: 0.2cm;
        }


        .detalle .tabla {
            font-size: 10px;
            border-collapse: collapse;
        }

        .detalle .tabla th {
            background: #565656;
            color: #fff;
        }

        .detalle .tabla th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }
        .cabeza td{
            border:none;
            text-align: left;
        }

        .divisor {
            margin-top: 6.5cm;
        }

        .footer {
            margin-top: 1cm;
            padding: 10px;
            text-align: center;
        }

        .observacion {
            float: left;
            width: 500px;
            text-align: center;
        }

        .observacion p {
            padding: 14px;
            text-align: justify !important;
            background-color: #f2f2f2;
        }

        .footer-table {
            float: right;
            margin-right: 5cm;

        }

        .footer-table table {
            border: 1px solid #000;
            border-radius: 8px;
            width: 300px;
        }

        .footer-table td {
            border: none !important;
            text-align: left;
        }
    </style>
</head>

<body>

    <div class="contenedor">
        <div class="numero">
            <p><b> Nro Liquidación </b> <span>{{ $factura->num_liquidacion }}</span></p>
        </div>
        <div class="img">
            @if ($factura->locatario == 1)
            <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}" width="300px">
            @elseif ($factura->locatario == 2)
            <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}" width="300px">
            @elseif ($factura->locatario == 3)
            <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}" width="300px">
            @else
            <img src="{{ storage_path('app/public/images/alba.png') }}" width="300px">
            @endif

            <div class="datos">
                <table style="border:none;" class="cabeza">
                    <tr>
                        <td><b>Datos del Prestador / Proveedor</b></td>
                        <td colspan="5">{{ $factura->cuit ." - " . $factura->razon_social}}</td>
                    </tr>
                    <tr>
                        <td><b>Fecha Liquidación</b></td>
                        <td> {{ date('d/m/y', strtotime($factura->fecha_registra)) }}</td>
                        <td><b>Número Factura</b></td>
                        <td>{{$factura->numero}}</td>
                        <td><b>Comprobante</b></td>
                        <td>{{$factura->comprobante}}</td>
                    </tr>
                    <tr>
                        <td><b>Razón Social</b></td>
                        <td colspan="2">{{$factura->r_social}}</td>
                        <td><b>Locatario</b></td>
                        <td colspan="2">{{$factura->locatario}}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="divisor">
            <p>-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</p>
        </div>
        <div class="detalle">
            <table class="tabla">
                <thead>
                    <tr>
                        <th width="90px">COD PRACTICA </th>
                        <th width="160px">PRACTICA</th>
                        <th width="70px">IMPORTE FACTURADO</th>
                        <th width="70px">IMPORTE APROBADO</th>
                        <th width="40px">IMPORTE DÉBITO</th>
                        <th width="100px">MOTIVO DEBITO</th>
                        <th width="120px">OBSERVACIONES DÉBITO</th>
                        <th width="60px">FECHA PRESTACIÓN</th>
                        <th width="70px">DNI</th>
                        <th width="150px">AFILIADO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detalle as $row)
                    <tr>
                        <td>
                            {{ $row->codigo_practica }}
                        </td>
                        <td>{{ $row->practica }}</td>
                        <td>

                            {{ $row->monto_facturado}}
                        </td>
                        <td>
                            {{ $row->monto_aprobado}}
                        </td>
                        <td>
                            {{$row->monto_debitado}}
                        </td>
                        <td>
                            {{ $row->motivo_debito}}
                        </td>
                        <td>
                            {{ $row->observacion_debito }}
                        </td>
                        <td>
                            {{ date('d/m/Y', strtotime($row->fecha_prestacion)) }}
                        </td>
                        <td>
                            {{ $row->dni_afiliado}}
                        </td>
                        <td>
                            {{ $row->afiliado}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="footer">
            <div class="observacion">
                <spasn>Observaciones de Auditoria</spasn>
                <p>Se cubrieron los servicios solicitados, incluyendo consultas, estudios y
                    medicamentos. Los costos están detallados y se aplicaron los descuentos
                    correspondientes.</p>
            </div>
            <div class="footer-table">
                <p>Resumen</p>
                <table>
                    <tr>
                        <td>Comprobante:</td>
                        <td>{{number_format(($factura->subtotal ?? 0),2, ',', '.') }} </td>
                    </tr>
                    <tr>
                        <td>Debitos:</td>
                        <td>{{ number_format($factura->total_debitado ?? 0, 2, '.', '') }}</td>
                    </tr>
                    <tr>
                        <td>IVA:</td>
                        <td>{{ number_format(($factura->total_iva?? 0),2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Total a Pagar:</td>
                        <td>
                            {{ number_format(($factura->total_neto ?? 0) - (($factura->total_debitado ?? 0) != 0 ? $factura->total_debitado : 0), 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="divisor-footer">
                <p>-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</p>
            </div>
        </div>
    </div>

</body>

</html>