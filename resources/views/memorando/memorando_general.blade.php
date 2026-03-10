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
            <p class="juntar"><b>A:</b> Secretario de Obra Social y Previsión Social Fernando Gainza</p>
            <p><b>Área:</b> Auditoría Médica</p>
            <hr>
            <p><b>De:</b> Belén Perrone y Celeste Perrone</p>
            <p><b>Área:</b> Afiliaciones</p>
            <hr>
            <p><b>TEMA:</b> Altas mensuales de beneficiarios</p>
            <p class="texto">De nuestra consideración: Adjuntamos informes con las bases de Obra Social para los siguientes prestadores</p>

            <hr class="color">
            <div class="datos " style="page-break-inside: avoid;">
                <br>
                <p><b>Detalle de cápitas:</b></p>
                <br>
                @foreach($locatarios as $nombreLocatorio => $filiales)

                @php
                $totalLocatorio = 0;
                $rowspan = $filiales->count();
                @endphp
                <div style="page-break-inside: avoid;">
                    <table style="width:100%; table-layout:fixed; border-collapse: collapse; border:1px solid #000; margin-bottom:15px;">
                        <colgroup>
                            <col style="width:40%">
                            <col style="width:40%">
                            <col style="width:20%">
                        </colgroup>
                        <tbody>

                            @foreach($filiales as $index => $item)

                            @php $totalLocatorio += $item->total_afiliados; @endphp

                            <tr>
                                @if($index == 0)
                                <td rowspan="{{ $rowspan +1 }}" style="border:1px solid #000; padding:6px;">
                                    {{ $nombreLocatorio }}
                                </td>
                                @endif

                                <td style="border:1px solid #000; padding:6px;">
                                    {{ $item->delegacion }}
                                </td>

                                <td style="border:1px solid #000; padding:6px; text-align:right;">
                                    {{ $item->total_afiliados }}
                                </td>

                            </tr>

                            @endforeach
                            <tr style="font-weight:bold; background:#f2f2f2;">
                                <td style="border:1px solid #000; padding:6px; text-align:right;">
                                    TOTAL
                                </td>
                                <td style="border:1px solid #000; padding:6px; text-align:right;">
                                    {{ $totalLocatorio }}
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                @endforeach
            </div>
        </div>
    </div>
</body>

</html>