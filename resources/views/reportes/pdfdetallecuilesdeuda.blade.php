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
            <td class="text-center"> <img style='' src='{{ storage_path('app/public/images/ospf.png')  }}' alt='no_image' width='70px'
                    height='60px'></td>
        </tr>
        <tr>

            <td class="text-center">
                <h2 class="fm text-center" style='color:#28A745;font-size:28px;'>O.S.P.F</h2>
                <span class="fm text-center" style='font-size:11px'>OBRA SOCIAL DEL PERSONAL DE FARMACIA</span><br>
                <span class="fm text-center" style='font-size:11px'>R.N.O.S 1-0740</span><br>
                <span class="fm text-center" style='font-size:11px'>CUIT 33-64810438-9</span>
            </td>
        </tr>
    </table>
    <table style='width: 100%;' class="fm">
        <tr class="fm text-center">
            <td class="fm text-center"><h4>DETALLE DE CUILES DE LIQUIDACION DE DEUDA</h4></td>
        </tr>
    </table>

    <table id="customers">
        <thead>
            <tr>
                <th>Periodo</th>
                <th>CUIL</th>
                <th>Fecha Presentación</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($detalle as $row )
                <tr>
                    <td>{{ $row->periodo }}</td>
                    <td>{{ $row->cuil }}</td>
                    <td>{{ $row->fecpresent }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
