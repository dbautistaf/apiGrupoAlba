<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Memorando</title>
    <style>
        * {
            margin-top: 0.8cm;
            margin-left: 0.5cm;
            margin-bottom: 0.2cm;
            box-sizing: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 15px;
        }

        .contenedor {
            display: inline-block;
            text-align: center;
            width: 90%;
            margin-top: -1.5cm;
        }

        .contenedor .img hr {
            margin-top: 1cm;
            margin-bottom: 1cm;
        }

        .img img {
            margin-left: -4cm;
            position: relative;
            max-width: 100px;
            max-height: 90px;
        }

        .img .fecha {
            float: right;
            margin-right: 50px;
            margin-top: 2cm;
        }

        .img .titulo {
            margin-left: 43%;
            margin-top: 1.5cm;
            margin-bottom: 1cm;
            ;
        }

        .img .titulo u b {
            font-size: 18px;
        }

        .img p {
            text-align: left;
            margin-left: 40px;
        }

        .img .juntar {
            margin-bottom: -20px;
        }

        .img .color {
            color: #17A64F;
        }

        .img .texto {
            width: 500px;
            margin-left: 60px;
        }

        .datos {
            margin-top: 50px;
            margin-bottom: 2cm;
        }

        .datos hr {
            color: #17A64F;
        }

        .datos table {
            border: solid 1px #333;
        }

        .footer {
            position: fixed;
            bottom: 2cm;
            left: 1.5cm;
            right: 1cm;
            height: 80px;
            text-align: center;
            font-size: 12px;
        }

        .footer hr {
            color: #51a8b1;
        }

        thead {
            display: table-header-group;
        }
    </style>
</head>

<body>
    @php
    use Carbon\Carbon;
    Carbon::setLocale('es');
    $fechaFormateada = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
    @endphp
    <div class="contenedor">
        <div class="img">
            <img src="{{ storage_path('app/public/images/ospf.png') }}">
            <p class="fecha"><b>Fecha:</b> {{ $fechaFormateada }}</p>
            <p class="titulo"><u><b>Memorando</b></u></p>
            <hr>
            <p class="juntar"><b>A:</b> Secretaría de Obra Social y Previsión Social</p>
            <p><b>Área:</b> Auditoría Médica</p>
            <hr>
            <p><b>Área:</b> Afiliaciones</p>
            <hr>
            <p><b>TEMA:</b> Altas mensuales de beneficiarios</p>
            <p class="texto">De nuestra consideración: Adjuntamos informes con las altas mensuales de beneficiarios.</p>

            <hr class="color">
            <div class="datos " style="page-break-inside: avoid;">
                @if ($tipo == "alta")
                <p><u><b>Detalle de altas:</b></u></p>
                @elseif ($tipo == "baja")
                <p><u><b>Detalle de bajas:</b></u></p>
                <p><u><b>Periodo</b>: {{$periodo}}</b></u></p>
                @endif
                <table style="width:100%; border-collapse: collapse; border:1px solid #000;">
                    <thead>
                        <tr style="font-size: 11px;">
                            <th style="border:1px solid #000; padding: 8px; text-align: center;">
                                Filial
                            </th>
                            <th style="border:1px solid #000; padding: 8px; text-align: center;">
                                Nombre
                            </th>
                            <th style="border:1px solid #000; padding: 8px; text-align: center;">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $granTotal = 0; @endphp

                        @foreach($data as $item)
                        @php $granTotal += $item->total_afiliados; @endphp
                        <tr style="font-size: 11px;">
                            <td style="border:1px solid #000; padding: 6px; text-align:center;">
                                {{ $item->id_filial }}
                            </td>
                            <td style="border:1px solid #000; padding: 6px;">
                                {{ $item->delegacion }}
                            </td>
                            <td style="border:1px solid #000; padding: 6px; text-align:right;">
                                {{ $item->total_afiliados }}
                            </td>
                        </tr>
                        @endforeach

                        <tr style="font-weight:bold; font-size: 12px; background:#f2f2f2;">
                            <td colspan="2" style="border:1px solid #000; padding: 8px; text-align:right;">
                                TOTAL GENERAL
                            </td>
                            <td style="border:1px solid #000; padding: 8px; text-align:right;">
                                {{ $granTotal }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="footer">
                <hr>
                <table style="width:100%; text-align:center; font-size:12px;">
                    <tr>
                        <td style="width:33%;">Fecha</td>
                        <td style="width:33%;">Firma</td>
                        <td style="width:33%;">Aclaración</td>
                    </tr>
                </table>
                <hr>
            </div>
        </div>
    </div>
</body>

</html>