<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
     <style>
        @font-face {
            font-family: 'quicksand';
            src: url('/fonts/Quicksand-Regular.ttf') format('truetype');
        }
        #customers {
            border-collapse: collapse;
            width: 100%;
            font-family: 'quicksand';
            font-family: Arial, Helvetica, sans-serif !important;
        }

        .fm{
            font-family: Arial, Helvetica, sans-serif !important;
        }
        .text-center{
            text-align: center !important;
        }

        #customers td,
        #customers th {
            border: 1px solid #333C48;
            padding: 5px;
            text-align: center;
             font-family: 'quicksand';
             font-size: 9px
        }

        #table-details {
            border-collapse: collapse;
            width: 100%;
            margin-top: 0px;
             font-family: 'quicksand';
        }

        #table-details {
            border-collapse: collapse;
            width: 100%;
            margin-top: 0px;
             font-family: 'quicksand';
        }

        #table-details tbody tr td {
            height: 245px !important;
            border-left: 1px solid #333C48;
            border-right: 1px solid #333C48;
            border-bottom: 1px solid #333C48;
        }
    </style>
</head>
<body>

    <table style='width: 30%;' class="fm">
        <tr class="text-center">
            <td class="text-center"> <img style='' src='{{ storage_path('app/public/images/alba.png')  }}' alt='no_image' width='160px'
                    height='80px'></td>
        </tr>
        <tr>

            <td class="text-center">
               {{-- <h2 class="fm text-center" style='color:#28A745;font-size:28px;'>O.S.P.F</h2> --}}
                <span class="fm text-center" style='font-size:11px'>OBRA SOCIAL DEL PERSONAL DE FARMACIA</span><br>
                <span class="fm text-center" style='font-size:11px'>R.N.O.S 1-0740</span><br>
                <span class="fm text-center" style='font-size:11px'>CUIT 33-64810438-9</span>
            </td>
        </tr>
    </table>
    <table style='width: 100%;' class="fm">
        <tr class="fm text-center">
            <td class="fm text-center"><h4>LIQUIDACIÓN POR ACTUALIZACIÓN DE DEUDA (INTERNO PARA DELEGACIÓN)</h4></td>
        </tr>
    </table>
    <table tyle='width: 100%;' class="fm">
        <tr>
            <td style="font-size: 11px;">DEUDA POR APORTES Y CONTRIBUCIONES DE LEY 23660</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">LIQUIDACION ACTUALIZADA AL: {{ date('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">RES.578/04 - RES492/06 - RES841/2010 - RES50/2019</td>
        </tr>
    </table>
    <hr>
    <table tyle='width: 100%;' class="fm">
        <tr>
            <td style="font-size: 11px;">RAZÓN SOCIAL</td>
            <td width="465px" style="font-size: 11px;">: {{ $empresa->razon_social }}</td>
            <td style="font-size: 11px;"><b style="text-align: right;font-size: 9px;">DATOS OBTENIDOS DE ARCA</b></td>
        </tr>
        <tr>
            <td style="font-size: 11px;">CUIT </td>
            <td style="font-size: 11px;" colspan="2">: {{ $empresa->cuit }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">NOMBRE COMERCIAL</td>
            <td style="font-size: 11px;" colspan="2">: {{ $empresa->nombre_fantasia }}</td>
        </tr>
         <tr>
            <td style="font-size: 11px;">DOMICILIO </td>
            <td style="font-size: 11px;" colspan="2">: {{ $empresa->domicilio }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">LOCALIDAD</td>
            <td style="font-size: 11px;" colspan="2">: {{ $empresa->localidad->nombre }}</td>
        </tr>
    </table>
    <hr>
    @php
        $consolidado = 0;
    @endphp
    <table class="fm" id="customers">
        <thead>
            <tr>
                <th>AAMM</th>
                <th>REMUNERACION</th>
                <th>APORTES Y CONTRIBUCIONES</th>
                <th>TOTAL TRAB</th>
                <th>RES. 841/2010</th>
                <th>RES. 50/2019</th>
                <th>RES. 598/2019</th>
                <th>RES. 324</th>
                <th>RES. 828/25</th>
                <th>DEUDA CONSOLIDADA</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datos as $key)
                <tr>
                    <td>{{ $key->periodo}}</td>
                    <td>{{ number_format($key->importe_sueldo, 2, ',', '.') }}</td>
                    <td>{{ number_format($key->contribucion, 2, ',', '.') }}</td>
                    <td>{{ $key->cant_empleados }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ number_format($key->monto_deuda, 2, ',', '.') }}</td>
                </tr>
                {{ $consolidado += $key->monto_deuda }}
            @endforeach

        </tbody>
    </table>

    <div class="l" style="width: 98%; border: 1px solid #3d3d3d; border-radius: 8px; text-align: center;margin-top: 20x;">
    <table class="fm" style="margin-top: 10px; margin-left: 70px;">
        <tr>
            <td style="font-size: 11px;">INTERESES TOTALES:</td>
            <td width="250px" style="font-size: 11px;">{{  number_format(0, 2, ',', '.') }}</td>
            <td style="font-size: 11px;">IMPORTE TOTAL:</td>
            <td style="font-size: 11px;">{{  number_format(0, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="font-size: 11px;">CAPITAL TOTAL:</td>
            <td style="font-size: 11px;">{{  number_format(0, 2, ',', '.') }}</td>
            <td style="font-size: 11px;">CONSOLIDADO:</td>
            <td style="font-size: 11px;">{{  number_format($consolidado, 2, ',', '.') }}</td>
        </tr>
    </table>
</div>
</body>
</html>
