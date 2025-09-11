<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Orden de Pago - {{ $comprobante_nro }}</title>
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
    <table style="width: 100%; border-collapse: collapse">
        <tbody>
            <tr style="border: 2px solid #333">
                <td width="43%" style="font-size: 11px; padding-left: 10px">
                    <div style="text-align: center; margin-bottom: 10px; font-size:20px">
                        @if ($razon_social->id_razon == 1)
                            <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @elseif ($razon_social->id_razon == 2)
                            <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}" width="90px">
                        @elseif ($razon_social->id_razon == 3)
                            <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @elseif ($razon_social->id_razon == 5)
                            <img src="{{ storage_path('app/public/images/alba.jpeg') }}" width="120px">
                        @elseif ($razon_social->id_razon == 6)
                            <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}" width="120px">
                        @else
                            <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}" width="120px">
                        @endif
                    </div>
                    <div><strong>Razón Social:</strong> {{ $razon_social->razon_social }}</div>
                    <div>
                        <strong>Condición frente al IVA:</strong> {{ $razon_social->iva }}
                    </div>
                    <div>
                        <strong>Domicilio Comercial:</strong> {{ $razon_social->domicilio }}
                    </div>
                </td>
                <td style="padding: 0px; width: 14%">
                    <div
                        style="
                                border: 2px solid #333;
                                text-align: center;
                                margin-top: -1px;
                                line-height: 11px;
                            ">
                        <h1>O</h1>
                        <p style="font-size: 11px;">Cod. {{ $comprobante_nro }}</p>
                    </div>
                    <div
                        style="
                                text-align: center;
                                padding: 0;
                                margin: 0;
                                line-height: 15px;
                            ">
                        |<br />|<br />|<br />
                    </div>
                </td>
                <td width="43%"
                    style="
              font-size: 11px;
              padding: 10px 10px 0px 20px;
            ">
                    <div style="font-size: 14px;">ORDEN DE PAGO:</div>
                    <div style="font-size: 15px;">Comp. Nro: {{ substr($comprobante_nro, 4) }}</div>
                    <div>
                        <strong>Fecha de Emisión:</strong> {{ $fecha_emision }}
                    </div>
                    <div><strong>CUIT:</strong> {{ $razon_social->cuit }}</div>
                    <div><strong>Ingresos Brutos:</strong> Exento</div>
                    <div><strong>Fecha de inicio de Actividades:</strong> 01/11/2007</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="border: 2px solid #333; margin-top: -1.5px; padding: 10px 10px; font-size: 12px;">
        <div style="line-height: 1.5rem">
            <span>
                CUIT: &nbsp; {{ $cuit_proveedor }}
            </span>
            <span style="margin-left: 20px;">Apellido y Nombre / Razón Social: &nbsp; {{ $nombre_proveedor }}</span>
        </div>
        <div style="line-height: 1.5rem;">
            <span>Condición frente al IVA: &nbsp; {{ $iva_proveedor }}</span>
            <span style="margin-left: 20px;">Domicilio Comercial: &nbsp; {{ $domicilio_proveedor }}</span>
        </div>
    </div>

    <table>
        <tbody>
            <tr style="font-size: 11px;">
                <td width="50%" style="padding: 20px">Aplicado a:</td>
                <td width="50%" style="padding: 20px">
                    Se entregaron los siguientes valores
                </td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <td style="width: 45%; padding: 0px;">
                    <table style="position: static;">
                        <thead>
                            <tr style="font-size: 11px;">
                                <th style="border: 2px solid #333; padding: 15px 0px; text-align: center; width: 50%;">
                                    Detalle / Factura
                                </th>
                                <th style="border: 2px solid #333; padding: 15px 0px; text-align: center; width: 18%;">
                                    Cuota Nº
                                </th>
                                <th style="border: 2px solid #333; padding: 15px 0px; text-align: center; width: 25%;">
                                    Importe
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 1;
                            @endphp

                            @foreach ($facturas as $item)
                                <tr style="border: 2px solid #333; font-size: 11px; padding: 5px;">
                                    <td style="height: 25px;">
                                        FAC{{ $item->tipo_letra }}
                                        {{ $item->sucursal }}-{{ str_pad($item->numero, 8, '0', STR_PAD_LEFT) }}
                                        - (LIQ N° {{ $item->num_liquidacion }})
                                    </td>
                                    <td style="text-align: center;height: 25px;">
                                        1
                                    </td>
                                    <td style="text-align: center;height: 25px;">
                                        {{ number_format($item->total_neto, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @php
                                    $contador++;
                                @endphp
                            @endforeach

                            @while ($contador <= 14)
                                <tr style="border: 2px solid #333;">
                                    <td style="height: 25px;" colspan="3"></td>
                                </tr>
                                @php
                                    $contador++;
                                @endphp
                            @endwhile
                        </tbody>
                    </table>
                    <div
                        style="
                border: 2px solid #333;
                margin-top: 4px;
                padding: 5px;
                text-align: right;
                font-size: 12px;
              ">
                        Débito: &nbsp; &nbsp; &nbsp; ${{ $debito ?? 0, 2, ',', '.' }}
                    </div>
                    <div
                        style="
                border: 2px solid #333;
                margin-top: 4px;
                padding: 8px;
                text-align: right;
                font-size: 12px;
              ">
                        Total a Pagar: &nbsp; &nbsp; &nbsp;
                        ${{ number_format(($total ?? 0) - ($debito ?? 0), 2, ',', '.') }}
                    </div>
                </td>
                <td width="2%"></td>
                <td width="60%">
                    <table>
                        <thead>
                            <tr>
                                <th style="
                      border: 2px solid #333;
                      padding: 15px 0px;
                      text-align: center;
                      font-size: 11px;
                    "
                                    width="70%">
                                    Descripción
                                </th>
                                <th style="
                      border: 2px solid #333;
                      padding: 15px 0px;
                      text-align: center;
                      font-size: 11px;
                    "
                                    width="30%">
                                    Importe descontando el débito
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador2 = 1;
                                $contadorfinal2 = 14;
                      font-size: 11px;" width="30%">
                                    Importe descontando el débito
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador2 = 1;
                                $contadorfinal2 = 14;

                                if (!empty($pagos) && count($pagos) > 0) {
                                    $contadorfinal2 = 11;
                                }
                            @endphp

                            @foreach ($pagos as $item)
                                <tr style="border: 2px solid #333; font-size: 11px;">
                                    <td style="height: 25px;">
                                        @if ($item->id_forma_pago == 1)
                                            {{ $item->formaPago->tipo_pago }} - {{ $item->cuenta->nombre_cuenta }}
                                            {{ $item->cuenta->entidadBancaria->descripcion_banco }}
                                        @elseif ($item->id_forma_pago == 2)
                                            {{ $item->formaPago->tipo_pago }} - {{ $item->num_cheque }}
                                        @else
                                            {{ $item->formaPago->tipo_pago }} - {{ $item->cuenta->nombre_cuenta }}
                                            {{ $item->cuenta->entidadBancaria->descripcion_banco }}
                                            {{ $item->observaciones }}
                                        @endif
                                    </td>
                                    <td style="height: 25px; text-align: center;">
                                        {{ number_format(($item->monto_pago ?? 0) - ($debito ?? 0), 2, ',', '.') }}
                                    </td>
                                </tr>
                                <tr style="border: 2px solid #333; font-size: 11px">
                                    <td colspan="2" style="height: 25px;">
                                        Fecha de Pago: {{ $item->fecha_confirma_pago }}
                                    </td>
                                </tr>
                                @if ($item->id_forma_pago == 1)
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                            Destinatario
                                        </td>
                                    </tr>
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                            CUIT Destinatario: {{ $cuit_proveedor }}
                                        </td>
                                    </tr>
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                            CBU Destinatario: {{ $cbu_proveedor }}
                                        </td>
                                    </tr>
                                @else
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                        </td>
                                    </tr>
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                        </td>
                                    </tr>
                                    <tr style="border: 2px solid #333; font-size: 11px">
                                        <td colspan="2" style="height: 25px;">
                                        </td>
                                    </tr>
                                @endif

                                @php
                                    $contador2 = $contador2 + 2;
                                @endphp
                            @endforeach

                            @while ($contador2 <= $contadorfinal2)
                                <tr style="border: 2px solid #333">
                                    <td style="height: 25px;" colspan="2"></td>
                                </tr>
                                @php
                                    $contador2++;
                                @endphp
                            @endwhile
                        </tbody>
                    </table>
                    <div
                        style="
                border: 2px solid #333;
                margin-top: 4px;
                padding: 5px;
                text-align: right;
                font-size: 12px;
              ">
                        Débito: &nbsp; &nbsp; &nbsp; ${{ $debito ?? 0, 2, ',', '.' }}
                    </div>
                    <div
                        style="
                border: 2px solid #333;.
                margin-top: 10px;
                padding: 8px;
                text-align: right;
                font-size: 12px;
              ">
                        Total: &nbsp; &nbsp; &nbsp;${{ number_format(($total ?? 0) - ($debito ?? 0), 2, ',', '.') }}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div>
        <p>
            <span style="font-size: 12px; text-decoration: underline">Observaciones:</span>
            {{ $observaciones }}
        </p>
    </div>
</body>

</html>
