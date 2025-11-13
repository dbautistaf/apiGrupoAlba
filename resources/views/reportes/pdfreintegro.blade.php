<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         #table {
            border-collapse: collapse;
            width: 80%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        #table tr td{
            font-size: 12px;
            padding: 4px !important;
        }
    </style>
</head>
<body>
    <table style='font-family:quicksand; width: 100%;'>
        <tr>
            <td><img style='' src='{{ storage_path('app/public/images/ospf.png')  }}' alt='no_image' width='90px'
                    height='80px'></td>
            <td style='text-align:left;'>
                <h2 style='color:#28A745;font-size:28px;'>O.S.P.F</h2>
                <span style='font-size:9px'>OBRA SOCIAL DEL PERSONAL DE FARMACIA</span>
            </td>

            <td style='width:400px; text-align:right;font-size:11px'></td>
        </tr>
    </table>
    <p style="width: 100% !important; text-align: center;"><h4 style="text-align: center;text-decoration:underline;">Información del reintegro</h4></p>


    <table id="table">
        <tr>
            <td>CUIL</td>
            <td>: {{ $reintegro->afiliado->cuil_benef }}</td>
        </tr>
        <tr>
            <td>DNI</td>
            <td>: {{ $reintegro->afiliado->dni }}</td>
        </tr>
        <tr>
            <td>APELLIDO Y NOMBRE(S)</td>
            <td>: {{ $reintegro->afiliado->nombre }}</td>
        </tr>
        <tr>
            <td>FILIAL</td>
            <td>: {{ empty($reintegro->afiliado->filial) ? '':$reintegro->afiliado->filial->nombre_sindicato }}</td>
        </tr>
        <tr>
            <td>CBU TITULAR</td>
            <td>: {{ $reintegro->cbu_prestador}}</td>
        </tr>
        <tr>
            <td>NRO FACTURA</td>
            <td>: {{ $reintegro->afiliado->nombre }}</td>
        </tr>
        <tr>
            <td>IMPORTE SOLICITADO</td>
            <td>: {{ $reintegro->importe_solicitado }}</td>
        </tr>
        <tr>
            <td>IMPORTE RECONOCIDO</td>
            <td>: {{ $reintegro->importe_reconocido }}</td>
        </tr>
        <tr>
            <td>FECHA SOLICITUD</td>
            <td>: {{ \Carbon\Carbon::parse($reintegro->fecha_solicitud)->format('d/m/Y')  }}</td>
        </tr>
        <tr>
            <td>FECHA TRANSFERENCIA</td>
            <td>: {{ empty($reintegro->fecha_transferencia) ? '' :  \Carbon\Carbon::parse($reintegro->fecha_transferencia)->format('d/m/Y')}}</td>
        </tr>
        <tr>
            <td>OBSERVACIONES</td>
            <td>: {{ $reintegro->observaciones }}</td>
        </tr>
        <tr>
            <td>MOTIVO REINTEGRO</td>
            <td>: {{ $reintegro->motivo }}</td>
        </tr>
        <tr>
            <td>OBSERVACIONES AUDITORIA</td>
            <td>: {{ $reintegro->observaciones_auditoria }}</td>
        </tr>
         <tr>
            <td>AUTORIZADO POR</td>
            <td>: {{ $reintegro->autorizado_por }}</td>
        </tr>
        <tr>
            <td>ESTADO</td>
            <td>: {{ $reintegro->estado }}</td>
        </tr>
    </table>

    <p style="width: 100%; text-align: right;font-size: 11px;border-top: 1px dashed #333C48;padding:4px; margin-top: 90px">Fecha Impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i')  }}</p>
</body>
</html>
