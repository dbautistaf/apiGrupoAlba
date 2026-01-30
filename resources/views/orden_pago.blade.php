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
                        @if ($facturas[0]?->detallefc->razonSocial->id_razon == 1)
                        <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @elseif ($facturas[0]?->detallefc->razonSocial?->id_razon == 2)
                        <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}" width="90px">
                        @elseif ($facturas[0]?->detallefc->razonSocial?->id_razon == 3)
                        <img src="{{ storage_path('app/public/images/alba.png') }}" width="120px">
                        @elseif ($facturas[0]?->detallefc->razonSocial?->id_razon == 5)
                        <img src="{{ storage_path('app/public/images/alba.jpeg') }}" width="120px">
                        @elseif ($facturas[0]?->detallefc->razonSocial?->id_razon == 6)
                        <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}" width="120px">
                        @else
                        <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}" width="120px">
                        @endif
                    </div>
                    <div><strong>Razón Social:</strong> {{ $facturas[0]?->detallefc->razonSocial?->razon_social }}</div>
                    <div>
                        <strong>Condición frente al IVA:</strong> {{ $facturas[0]?->detallefc->razonSocial?->iva }}
                    </div>
                    <div>
                        <strong>Domicilio Comercial:</strong> {{ $facturas[0]?->detallefc->razonSocial?->domicilio }}
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
                        <p style="font-size: 11px;">Cod. {{ $comprobante_nro }}</p>
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
                <td width="43%" style="
              font-size: 11px;
              padding: 10px 10px 0px 20px;
            ">
                    <div style="font-size: 14px;">ORDEN DE PAGO:</div>
                    <div style="font-size: 15px;">Comp. Nro: {{ substr($comprobante_nro, 4) }}</div>
                    <div>
                        <strong>Fecha de Emisión:</strong> {{ $fecha_emision }}
                    </div>
                    <div><strong>CUIT:</strong> {{$facturas[0]?->detallefc->razonSocial?->cuit }}</div>
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
                                <th style="border: 2px solid #333; padding: 15px 0; text-align: center; width: 50%;">
                                    Detalle / Factura
                                </th>
                                <th style="border: 2px solid #333; padding: 15px 0; text-align: center; width: 18%;">
                                    Cuota Nº
                                </th>
                                <th style="border: 2px solid #333; padding: 15px 0; text-align: center; width: 25%;">
                                    Importe
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                            $totalFilas = 0;
                            $maxFilas = 20;
                            @endphp

                            {{-- FACTURAS --}}
                            @foreach ($facturas as $item)
                            <tr style="border: 2px solid #333; font-size: 11px;">
                                <td style="height: 25px;">
                                    FAC{{ $item?->detallefc?->tipo_letra }}
                                    {{ $item?->detallefc?->sucursal }}-
                                    {{ str_pad($item?->detallefc?->numero, 8, '0', STR_PAD_LEFT) }}
                                    - (LIQ N° {{ $item?->detallefc?->num_liquidacion }})
                                </td>
                                <td style="text-align: center; height: 25px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td style="text-align: center; height: 25px;">
                                    {{ number_format($item?->detallefc?->total_neto ?? 0, 2, ',', '.') }}
                                </td>
                            </tr>
                            @php $totalFilas++; @endphp
                            @endforeach

                            @while ($totalFilas < $maxFilas)
                                <tr style="border: 2px solid #333;">
                                <td style="height: 25px;" colspan="3"></td>
            </tr>
            @php $totalFilas++; @endphp
            @endwhile

        </tbody>
    </table>
    <div style="
                border: 2px solid #333;
                margin-top: 4px;
                padding: 5px;
                text-align: right;
                font-size: 12px;
              ">
        Débito: &nbsp; &nbsp; &nbsp; ${{ number_format($debito ?? 0, 2, ',', '.') }}
    </div>
    <div style="
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
                    <th style="border: 2px solid #333; padding: 10px 0; text-align: center; font-size: 11px;" width="70%">
                        Descripción
                    </th>
                    <th style="border: 2px solid #333; padding: 10px 0; text-align: center; font-size: 11px;" width="30%">
                        Importe descontando el débito
                    </th>
                </tr>
            </thead>

            <tbody>
                @php
                $totalFilas = 0;
                $maxFilas = 20;
                @endphp

                @foreach ($pagos as $item)
                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td style="height: 25px;">
                        @if ($item?->id_forma_pago == 1)
                        {{ $item?->formaPago?->tipo_pago }} -
                        {{ $item?->cuenta?->nombre_cuenta }}
                        {{ $item?->cuenta?->entidadBancaria?->descripcion_banco }}
                        @elseif ($item?->id_forma_pago == 2)
                        {{ $item?->formaPago?->tipo_pago }} -
                        {{ $item?->num_cheque }}
                        @else
                        {{ $item?->formaPago?->tipo_pago }} -
                        {{ $item?->cuenta?->nombre_cuenta }}
                        {{ $item?->cuenta?->entidadBancaria?->descripcion_banco }}
                        @endif
                    </td>

                    <td style="height: 25px; text-align: center;">
                        {{ number_format(($item->monto_pago ?? 0) - ($debito ?? 0), 2, ',', '.') }}
                    </td>
                </tr>
                @php $totalFilas++; @endphp
                @foreach ($item->pagosParciales as $pagosP)
                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;">
                        Fecha probable de Pago:
                        {{ $item->fechaprobablepagos[$loop->index]?->fecha_probable_pago }}
                        | Fecha de Pago:
                        {{ $pagosP?->fecha_confirma_pago }}
                    </td>
                </tr>
                @php $totalFilas++; @endphp

                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;">
                        Forma de Pago:
                        {{ $pagosP?->formaPago?->tipo_pago }}
                        | Monto de Pago:
                        {{ number_format($pagosP?->monto_pago ?? 0, 2, ',', '.') }}
                    </td>
                </tr>
                @php $totalFilas++; @endphp
                @endforeach
                @if ($item?->id_forma_pago == 1)
                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;">Destinatario</td>
                </tr>
                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;">CUIT Destinatario: {{ $cuit_proveedor }}</td>
                </tr>
                <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;">CBU Destinatario: {{ $cbu_proveedor }}</td>
                </tr>
                @php $totalFilas += 3; @endphp
                @else
                @for ($i = 0; $i < 3; $i++)
                    <tr style="border: 2px solid #333; font-size: 11px; height: 25px;">
                    <td colspan="2" style="height: 25px;"></td>
                    </tr>
                    @endfor
                    @php $totalFilas += 3; @endphp
                    @endif

                    @endforeach
                    @while ($totalFilas < $maxFilas)
                        <tr style="border: 2px solid #333; height: 25px;">
                        <td colspan="2" style="height: 25px;"></td>
                        </tr>
                        @php $totalFilas++; @endphp
                        @endwhile

            </tbody>
        </table>
        <div style="
                border: 2px solid #333;
                margin-top: 4px;
                padding: 5px;
                text-align: right;
                font-size: 12px;
              ">
            Débito: &nbsp; &nbsp; &nbsp; ${{ number_format($debito ?? 0, 2, ',', '.') }}
        </div>
        <div style="
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

    {{-- <div>
        <p>
            <span style="font-size: 12px; text-decoration: underline">Observaciones:</span>
            {{ $observaciones }}
    </p>
    </div> --}}
</body>

</html>