<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mesa entrada</title>
    <style>
        * {
            margin: 7px;
            box-sizing: 0;
        }

        body {

            padding: 7px;
            border-radius: 10px
        }

        .item {
            padding: 7px;
            border: 1px solid #605f5f;
            border-radius: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
        }

        .line {
            font-family: 'Courier New', Courier, monospace;
            border: 1px solid #605f5f;
            padding: 1px;
            font-size: 10px;
            border-radius: 10px
        }

        .label,
        .parrafo,
        .subtitle,
        .firma {
            font-family: 'Courier New', Courier, monospace;
        }

        .label {
            font-weight: bold;
        }

        .parrafo {
            text-align: justify;
            font-size: 11px;
        }

        .firma {
            width: 180px;
            border-top: 1px dashed #605f5f;
            text-align: center;
            padding: 5px
        }
        .subtitle{
            position:absolute;
            font-size: 11px;
            top:100px;
            left:120px;
        }
        #table {
            font-family: 'Courier New', Courier, monospace;
            border-collapse: collapse;
            width: 100%;
        }

        #table th {
            border: 1px solid #555353;
            padding: 5px;
            background: rgb(104, 103, 103);
            color: #fff;
        }

        #table td {
            border: 1px solid #555353;
            padding: 9px;
            font-size: 11px;
        }

        .footer {
            position: absolute;
            bottom: 5px;
            text-align: center;
        }

        .footer .table-rigth {
            position: relative;
            margin-left: 450px;
            right: 5px;
        }
        .header .right{
            text-align: center;
            width: 500px;
        }
    </style>
</head>

<body>

    <div class="header">
        <table>
            <tr>
                <td width="300px">
                    <div class="left">
                        <img src="{{ storage_path('app/public/images/coqsa.png') }}" alt="no_image" width="150px" 
                            height="50px">
                    </div>
                </td>
                <td class="right">
                    <div>
                        <h5 style="font-family:'Courier New', Courier, monospace; font-size: 14px">Mesa de entrada "Ingreso de correspondencia"</h5>

                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="subtitle">
        <table>
            <tr>
                <td width="120"><b>Sector:</b></td>
                <td>{{$area}}</td>
            </tr>
            <tr>
                <td width="120"><b>Fecha Ingreso:</b></td>
                <td>{{date("d/m/Y", strtotime($desde))}}</td>
                <td> <b>a</b></td>
                <td>{{date("d/m/Y", strtotime($hasta))}}</td>
            </tr>
        </table>
    </div>
    <div class="detalle">
        <table id="table">
            <thead>
                <tr>
                    <th>N°</th>
                   <th>N° Ingreso</th>
                   <th>Area</th>
                   <th>Emisor</th>
                   <th>Documentacion</th>
                   <th>N° Factura</th>
                   <th>Importe</th>
                   <th>Fecha de Carga</th>
                   <th>Delegación</th>
                   <th>Recepción</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $index=1
                @endphp
                @foreach ($data as $item)
                    <tr>
                        <td>{{ $index }}</td>
                        <td>{{ $item->cod_mesa }}</td>
                        <td>{{ $item->tipoArea->tipo_area }}</td>
                        <td>{{ $item->emisor }}</td>
                        <td>{{ $item->tipoDocumento->tipo_documentacion }}</td>
                        <td>{{ $item->nro_factura }}</td>
                        <td>{{ $item->importe }}</td>
                        <td>{{date("d/m/Y", strtotime($item->fecha_carga))}}</td>
                        <td>{{ $item->sindicato->nombre_sindicato }}</td>
                        <td></td>
                    </tr>
                    @php
                        $index++
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
