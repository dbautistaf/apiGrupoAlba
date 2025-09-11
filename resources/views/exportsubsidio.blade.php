<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ExportSubsidio</title>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>N° LIQUIDACION </th>
                <th>CUIL BENEFICIARIO</th>
                <th>NOMBES BENEFICIARIO</th>
                <th>CUIT PRESTADOR</th>
                <th>RAZON SOCIAL</th>
                <th>PERIODO</th>
                <th>N° FACTURA</th>
                <th>FECHA EMISION</th>
                <th>CAE-CAI</th>
                <th>MONTO SOLICITADO</th>
                <th>MONTO RECONOCIDO</th>
                <th>CODIGO PRACTICA</th>
                <th>DEPENDENCIA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dtsubsidio as $row)
                <tr>
                    <td>
                        @if (isset($row->subsidiodisca->num_liquidacion))
                            {{ $row->subsidiodisca->num_liquidacion }}
                        @endif
                    </td>
                    <td>{{ $row->disca->cuil_beneficiario }}</td>
                    <td>
                        @if (isset($row->disca->afiliado->apellidos) && isset($row->disca->afiliado->nombre))
                            {{ $row->disca->afiliado->apellidos }} {{ $row->disca->afiliado->nombre }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->cuil_prestador))
                            {{ $row->disca->cuil_prestador }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->prestador->razon_social))
                            {{ $row->disca->prestador->razon_social }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->periodo_prestacion))
                            {{ $row->disca->periodo_prestacion }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->num_factura))
                            {{ $row->disca->num_factura }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->fecha_emision_comprobante))
                            {{ $row->disca->fecha_emision_comprobante }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->num_cae_cai))
                            {{ $row->disca->num_cae_cai }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->disca->monto_solicitado))
                            {{ $row->disca->monto_solicitado }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->subsidiodisca->importe_subsidiado))
                            {{ $row->subsidiodisca->importe_subsidiado }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->id_practica))
                            {{ $row->id_practica }}
                        @endif
                    </td>
                    <td>
                        @if (isset($row->dependencia))
                            {{ $row->dependencia }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
