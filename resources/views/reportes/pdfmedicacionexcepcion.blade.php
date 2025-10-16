<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pdf</title>
    <style>
        @font-face {
            font-family: 'quicksand';
            src: url('/fonts/Quicksand-Regular.ttf') format('truetype');
        }
        #customers {
            border-collapse: collapse;
            width: 100%;
            font-family: 'quicksand';
        }

        #customers td,
        #customers th {
            border: 1px solid #333C48;
            padding: 5px;
            text-align: center;
             font-family: 'quicksand';
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
    <table style='font-family:quicksand; width: 100%;'>
        <tr>
            <td><img style='' src='{{ storage_path('app/public/images/ospf.png')  }}' alt='no_image' width='90px'
                    height='80px'></td>
            <td style='text-align:left;'>
                <h2 style='color:#28A745;font-size:28px;'>O.S.P.F</h2>
                <span style='font-size:9px'>OBRA SOCIAL DEL PERSONAL DE FARMACIA</span>
            </td>

            <td style='width:400px; text-align:right;font-size:11px'>Pagina: 1/1</td>
        </tr>
    </table>
   @php
        $fechaAutoriza = null;
        $userAutoriza = '';
    @endphp
    <table style='border:1px solid #333C48;width:100%; border-radius:5px; padding:10px '>
        <tr style='border-radius:5px;'>
            <td style='width:90px; font-weight: bold;'>Tipo Trámite:</td>
            <td style='width:300px'>{{ ($receta->tipo_tramite == '1' ? 'AMBULATORIO' : ($receta->tipo_tramite == '2' ? 'PMI' :'AUTORIZADO' ) )}}</td>
            <td style='width:90px; font-weight: bold;'>Nro. Trámite:</td>
            <td style='width:140px'>{{ $receta->numero_tramite }}</td>
        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:90px;font-weight: bold;'>Prioridad:</td>
            <td style='width:300px'>TRAMITE NORMAL</td>
            <td style='width:90px;font-weight: bold;'>Fecha:</td>
            <td style='width:140px'>{{ \Carbon\Carbon::parse($receta->fecha_prescripcion)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <table style='border:1px solid #333C48;width:100%; border-radius:5px; padding:10px; margin-top:7px'>
        <tr>
            <th style='text-align:left;' colspan='4'>DATOS DEL BENEFICIARIO</th>
        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:145px'>Nombre y Apellidos:</td>
            <td style='width:350px'>{{   $receta->persona->apellidos  . ' ' . $receta->persona->nombre }}</td>
            <td style='width:150px'>Nro. Beneficiario:</td>
            <td>{{ $receta->persona->cuil_benef }}</td>
        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:145px'>Documento:</td>
            <td style='width:350px'>{{ $receta->persona->dni }}</td>
            <td style='width:150px'>Edad:</td>
            <td>{{ $receta->edad_afiliado }}</td>
        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:150px'>Plan:</td>
            <td style='width:100px'>{{ strtoupper($receta->plan_afiliado) }}</td>
        </tr>
    </table>

    <table style="border:1px solid #333C48;width:100%; border-radius:5px; padding:10px; margin-top:7px">
        <tr>
            <th style='text-align:left;' colspan='4'>DATOS DEL SOLICITANTE</th>
            <td rowspan='3' style='width:270px; border-left:0px solid #333C48'>
                {{-- <img style="margin-left: 19px" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($receta->numero_receta, 'EAN13') }}" alt="barcode" /> --}}
            </td>
        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:150px'>Nombre y Apellidos: </td>
            <td style='width:300px'>{{ $receta->medico_prescriptor }} </td>

        </tr>
        <tr style='border-radius:5px;'>
            <td style='width:150px'>Matricula:</td>
            <td style='width:100px'>{{ $receta->matricula }} </td>
        </tr>
       {{--  <tr style='border-radius:5px;'>
            <td style='width:150px'>Localidad</td>
            <td style='width:100px'> </td>
        </tr> --}}
    </table>

    <table id="customers" style='  margin-top:7px'>
        <tr>
            <td style=''>Monodroga + Presentacion</td>
            <td style='width:100px'>Código</td>
            <td style='width:100px'>Cantidad</td>
            <td style='width:100px'>Cobertura</td>
        </tr>
    </table>
    <table id="table-details">
        <tbody>
            <tr>
                <td style=''> </td>
                <td style='width:100px'> </td>
                <td style='width:100px'> </td>
                <td style='width:100px'></td>
            </tr>
        </tbody>
    </table>
    <table style=" width:100%; border-radius:5px; padding:10px;  margin-top: -250px;">
        <tbody>
            @foreach ($receta->detalle as $item )
            {{ $userAutoriza = $item->userautoriza;}}
            {{ $fechaAutoriza = $item->userautoriza == null ? '' : \Carbon\Carbon::parse($item->fecha_autoriza)->format('d/m/Y') }}
                <tr>
                <td style='text-align: center;'>{{ $item->laboratorio->monodroga->descripcion }} - {{ $item->laboratorio->presentacion }}</td>
                <td style='width:100px;text-align: center;'>{{ $item->laboratorio->troquel }}</td>
                <td style='width:100px;text-align: center;'>{{ $item->cantidad_autoriza }}</td>
                <td style='width:100px;text-align: center;'>{{ $item->cobertura }}</td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <table style='width:100%; padding:10px; margin-top:200px'>
        <tr>
            <td style='text-align:left;'><b>Trámite</b></td>
            <td style='text-align: center;width:100px'>{{ $receta->estado_autoriza == '1' ? 'AUTORIZADO' : $receta->estado_autoriza }}</td>
            <td style='text-align:left;'><b>Fecha de Autorización</b></td>
            <td style='text-align: center;width:100px'>{{ $fechaAutoriza }}</td>
            <td style='text-align: center'>
                < VALIDEZ 30 días>
            </td>
        </tr>
    </table>
    <table id="customers" style=' margin-top:6px'>
        <tr>
            <td style='text-align:left; height: 120px;'><b>Observaciones</b> {{ $receta->diagnostico }}</td>
            <td style='text-align:center; width: 180px;'><b>AUTORIZADO  A REALIZAR EN PRESTADOR  DE OSPF</b></td>
        </tr>
    </table>
    <table id="customers" style=' margin-top:1px'>
        <tr>
            <td style='text-align:left;font-size: 10px'>Ad referéndum de Auditoria Médica Central. Esta autorización debe adjuntarse a la facturación, junto con una copia de los informes o historia clínica. Validez de Autorización: 30  dias.</td>
        </tr>
    </table>
    <table id="customers" style='margin-top:1px'>
        <tr style="font-size: 11px">
            <td style='text-align:center; height: 150px;'>
                <div style="position: absolute; height: 145px;width: 250px; text-align: center;">
                    <p style="margin: 110px !important;"><b>Auditor: </b> {{ strtoupper($userAutoriza->nombre_apellidos) }}</p>
                    <p style="margin: 120px !important;font-size: 10px;">Firma y sello del Medico Auditor</p>
                </div>
            </td>
            <td style='text-align:center; width: 180px;'>
                <div style="position: absolute; height: 145px;width: 180px; text-align: center;left: 325px;">
                    {{-- <p style="margin: 110px !important;"><b>Auditor</b></p> --}}
                    <p style="margin: 130px !important;font-size: 10px;">Firma del Beneficiario</p>
                </div>
            </td>
            <td style='text-align:left; width: 250px;'>
                <div style="position: absolute; height: 145px;width: 250px; text-align: center;right: 25px;">
                    <div style="font-size: 13px; text-align: left;">
                        <p style="margin-bottom: 5px">Fecha de atención:..................../..................../....................</p>

                        <p>Diagnóstico:........................................................................ ..............................................................................................</p>
                    </div>
                    <p style="margin: 130px !important;font-size: 14px;">Firma, sello y Matricula del Efector</p>
                </div>
            </td>
        </tr>
    </table>
    <p style="width: 100%; text-align: right;font-size: 11px;border-top: 1px dashed #333C48;padding:4px; ">Fecha Impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i')  }}</p>
</body>

</html>
