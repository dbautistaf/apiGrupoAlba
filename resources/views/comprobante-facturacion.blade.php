<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Factura - {{ $comprobante_nro }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            color: #333;
        }

        .document-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .client-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 0px;
        }

        th,
        td {
            padding: 0px;
            text-align: left;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            border: 2px solid #333;
            width: 40%;
            float: right;
            padding: 5px;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <table>
        <tbody>
            <tr style="border: 2px solid #333">
                <td width="43%" style="font-size: 11px; padding-left: 10px">


                    <div style="text-align: center; margin-bottom: 10px">
                        @if ($locatario == 1)
                            <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}" width="120px">
                        @elseif ($locatario == 2)
                            <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}" width="90px">
                        @elseif ($locatario == 3)
                            <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}" width="120px">
                        @elseif ($locatario == 5)
                            <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @elseif ($locatario == 6)
                            <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @else
                            <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @endif
                    </div>
                    <div>
                        <strong>Razón Social:</strong> {{ $razon_social->razon_social }}
                    </div>
                    <div>
                        <strong>Condición frente al IVA:</strong> {{ $razon_social->iva }}
                    </div>
                    <div>
                        <strong>Domicilio Comercial:</strong> {{ $razon_social->domicilio }}
                    </div>
                </td>
                <td style="padding: 0px; width: 14%">
                    <div style="

                                border: 2px solid #333;
                                text-align: center;
                                margin-top: -1px;
                                line-height: 11px;
                            ">
                        <h1>O</h1>
                        <p style="font-size: 11px;">Cod. {{ $codigo_opa }}</p>
                    </div>
                    <div style="

                                text-align: center;
                                padding: 0;
                                margin: 0;
                                line-height: 15px;
                            ">
                        |<br />|<br />|<br />
                    </div>
                </td>
                <td style="

                            font-size: 11px;
                            padding: 10px 10px 0px 20px;
                            width: 43%;
                        ">
                    <div style="font-size: 15px">
                        Comp. Nro: {{ substr($codigo_opa, 4) }}
                    </div>
                    <div>
                        <strong>Fecha de Emisión:</strong> {{ $fecha_emision
                            }}
                    </div>
                    <div><strong>CUIT:</strong> {{ $cuit_proveedor }}</div>
                    <div><strong>Ingresos Brutos:</strong> Exento</div>
                    <div>
                        <strong>Fecha de inicio de Actividades:</strong>
                        01/11/2007
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="

                border: 2px solid #333;
                margin-top: -1.5px;
                padding: 10px 10px;
                font-size: 12px;
                line-height: 1.5rem
            ">
        <div>
            <span> CUIT: &nbsp; {{ $cuit_proveedor }} </span>
            <span style="margin-left: 40px;">Apellido y Nombre / Razón Social: &nbsp; {{
    $nombre_proveedor }}</span>
        </div>
        <div>
            <span>Periodo: &nbsp; {{ $periodo }}</span>
            <span style="margin-left: 30px">Tipo: &nbsp; {{ $tipo }}</span>
            <span style="margin-left: 40px">Sucursal: &nbsp; {{ $sucursal }}</span>
            <span style="margin-left: 40px">Factura: &nbsp; {{ $numero }}</span>
            <span style="margin-left: 10px">Fecha pago: &nbsp; {{$fecha_confirma_pago}}</span>
        </div>
    </div>

    <div style="padding: 20px; font-size: 12px;">Detalle factura:</div>

    <table>
        <thead>
            <tr style="font-size: 11px">
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 5%;">
                    Cod.
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 30%;">
                    Articulo
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 5%;">
                    Cantidad
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 10%;">
                    Precio Neto
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 12%;">
                    IVA
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 13%;">
                    SubTotal
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 12%;">
                    Monto Iva
                </th>
                <th style="border: 2px solid #333; padding: 10px 0px;text-align: center; width: 13%;">
                    Importe
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalle as $item)
                <tr style="
                                border: 2px solid #333;
                                font-size: 11px;
                                padding: 5px;
                            ">
                    <td style="height: 25px">{{ $item->id_articulo }}</td>
                    <td style="height: 25px">
                        {{ $item->articulo->descripcion_articulo }}
                    </td>
                    <td style="height: 25px; text-align: center">{{ $item->cantidad }}</td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->precio_neto, 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->iva, 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->subtotal, 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->monto_iva, 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->total_importe, 2, ',', '.') }}
                    </td>
                </tr>

            @endforeach

            @php
                $contador = 1;
            @endphp
            @while ($contador <= 4)
                <tr style="border: 2px solid #333">
                    <td style="height: 25px;" colspan="8"></td>

                </tr>
                @php
                    $contador++;
                @endphp
            @endwhile
        </tbody>
    </table>


    <div style="padding: 20px; font-size: 12px;">Detalle de Impuestos:</div>

    <table>
        <thead>
            <tr>
                <th style="border: 2px solid #333;padding: 10px 0px;text-align: center;font-size: 11px;width: 30%;">
                    Impuesto
                </th>
                <th style="border: 2px solid #333;padding: 10px 0px;text-align: center;font-size: 11px;width: 40%;">
                    %
                </th>
                <th style="border: 2px solid #333;padding: 10px 0px;text-align: center;font-size: 11px;width: 30%;">
                    Importe
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($impuesto as $item)
                <tr style="
                                border: 2px solid #333;
                                font-size: 11px;
                                padding: 5px;
                            ">
                    <td style="height: 25px">{{ $item->impuesto }}</td>
                    <td style="height: 25px">{{ $item->porcentaje }}</td>
                    <td style="text-align: center; height: 25px">
                        {{ number_format($item->importe, 2, ',', '.') }}
                    </td>
                </tr>

            @endforeach
        </tbody>
    </table>

    <br>

    <table style="width: 100%; border-collapse: collapse">
        <tbody>
            <tr>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: right;
                            width: 79%;
                        ">
                    Neto:
                </td>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: center;
                        ">
                    ${{ number_format($neto, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: right;
                            width: 79%;
                        ">
                    Impuestos:
                </td>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: center;
                        ">
                    ${{ number_format($impuestos, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: right;
                            width: 79%;
                        ">
                    Débitos:
                </td>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: center;
                        ">
                    ${{ number_format($descuentos, 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="

                            font-size: 14px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: right;
                            width: 79%;
                        ">
                    Total:
                </td>
                <td style="

                            font-size: 18px;
                            padding: 5px;
                            font-weight: bold;
                            text-align: center;
                        ">
                    ${{ number_format($total, 2, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>