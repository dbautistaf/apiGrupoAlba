<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RECETARIO</title>
    <style>
        body {
            font-family: 'centurygothic' !important;
            font-size: 14px;
            color: #333;
        }
        #table {
            border-collapse: collapse;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        #table tr td.border-left{
            border-left: 1px solid #333C48;
            padding: 4px !important;
        }
        #table tr td.border-right{
            border-right: 1px solid #333C48;
            padding: 4px !important;
        }
        #table tr td.border-top{
            border-top: 1px solid #333C48;
            padding: 4px !important;
        }
        #table tr td.border-bottom{
            border-bottom: 1px solid #333C48;
           padding: 4px !important;
        }
        .fz-9{
            font-size: 11px;
        }
        .fz-7{
            font-size: 10px;
        }
        .fz-6{
            font-size: 8px;
        }
        .pd-2{
            padding: 2px !important;
        }
        .text-center{
            text-align: center;
        }
        .text-left{
            text-align: left;
        }
        .text-right{
            text-align: right;
        }
        .fb{
            font-weight: bold;
        }
        .text-convenio{
            position: absolute;
            top: 300px;
            font-size: 45px;
            color: #fafffa00;
            z-index: -0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body>
    <p style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;width: 95%; text-align: right;font-size: 10px;border-top: 1px dashed #333C48; position: absolute; bottom: 0px;left:10px; ">Fecha Impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i')  }}</p>
    <table id="table">
        <tr>
            <td rowspan="2" class="border-left border-top border-bottom"><img  src="{{ storage_path('app/public/images/ospf.png')  }}" alt="no_image" width="70px" height="65x"></td>
            <td rowspan="2" class="border-top border-bottom" style="width: 210px">
                <h2 style="color:#28A745;font-size:28px;">O.S.P.F</h2>
                <span style="font-size:8px;">OBRA SOCIAL DEL PERSONAL DE FARMACIA</span>
            </td>
            <td class="border-top text-center">

                <p style="font-size:11px;">R.N.O.S. 1-0740-4</p>
                <span style="font-size:11px;">0-800-333-9820</span>

            </td>
            <td class="border-left border-bottom border-top border-right" colspan="2">
                <div style="position: relative;">
                    <div style="margin-right: 80px;">

                        <p style="font-size:13px;margin-left: 5px; width: 100px;">RECETA</p>
                        <p style="font-size:13px; top: 9px;">{{ $datos->nro_receta_api ?? $datos->numero_receta }}</p>

                    </div>
                    <img style="margin-left: 65px;" src="data:image/png;base64,{{ DNS1D::getBarcodePNG($datos->numero_receta, 'EAN13') }}" alt="barcode" />
                </div>
            </td>

        </tr>
        <tr>
            <td class="border-bottom border-right">

                <p style="font-size:12px">www.ospf.org.ar</p>

            </td>
            <td class="border-bottom border-right border-top"   width="200px">
                <p style="font-size:8px">Según resolución 310/2004, Ministerio de Salud</p>
            </td>
            <td class="border-bottom border-right border-top">
                <p class="fz-9 pd-2"><b>CÓDIGO DE BARRAS</b></p>
            </td>
        </tr>
    </table>
    <table id="table">
        <tr>
            <td class="border-left border-right fz-7 text-center fb" style="width: 233px;">APELLIDO Y NOMBRE DEL BENEFICIARIO</td>
            <td class=" border-right  fz-7 text-center fb" style="width: 189px;">CUIl</td>
            <td class=" border-right fz-7 fb" colspan="2">DIAGNÓSTICO</td>
        </tr>
        <tr>
            <td class="border-left border-bottom border-right fz-7 text-center" style="width: 233px;height: 40px;">{{ $datos->afiliado->nombre }} {{ $datos->afiliado->apellidos }}</td>
            <td class="border-bottom border-right  fz-7" style="width: 189px;">{{ $datos->afiliado->cuil_benef }}</td>
            <td class="border-bottom border-right fz-7" colspan="2"></td>
        </tr>
    </table>

    <table id="table">
        <tr>
            <td class="border-left border-bottom border-right fz-7 text-center fb" style="width: 116px;">Fecha de Emisión</td>
            <td class=" border-right border-bottom fz-7 text-center fb" style="width: 117px;">Número del Beneficiario</td>
            <td class=" border-right border-bottom fz-7 text-center fb" colspan="2"  style="width: 164px;">Plan</td>
            <td class=" border-right border-bottom fz-7 text-center fb" colspan="2" style="width: 40px;">Sexo</td>
            <td class=" border-right border-bottom fz-7 text-center fb" colspan="2" style="width: 40px;">Edad</td>
            <td class=" border-right border-bottom fz-7 text-center fb" colspan="2" style="width: 170px;">Fecha de dispensación</td>
        </tr>
        <tr>
            <td class="border-left border-right fz-7 text-center" style="width: 116px;height: 35px;"> {{ \Carbon\Carbon::parse($datos->fecha_solicita)->format('d/m/Y') }} </td>
            <td class=" border-right  fz-7 text-center" style="width: 117px;"> {{ $datos->afiliado->cuil_benef }}</td>
            <td class=" border-right fz-7 text-center" colspan="2"  style="width: 145px;"> {{   $datos->plan_afiliado  }}</td>
            <td class=" border-right fz-7 text-center" colspan="2" style="width: 40px;">{{ $datos->afiliado->id_sexo }} </td>
            <td class=" border-right fz-7 text-center" colspan="2" style="width: 40px;">{{ $datos->edad_afiliado }} </td>
            <td class=" border-right fz-7 text-center" colspan="2" style="width: 170px;"> </td>
        </tr>
    </table>
    @php
        $x = 1;
    @endphp
    @foreach ($datos->detalle as $rp )
        <table id="table">
        <tr>
            <td class="border-left border-top border-right fz-7 fb " style="width: 233px">RP{{ $x }}.</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 128px" colspan="2">Cant. de unidades</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 55px">Precio Unitario</td>
            <td class="border-top border-right fz-6 text-center fb" style="width: 55px">TOTAL</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 55px">DTO. % </td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 65px">MONTO FIJO<br>O.S.P.F</td>
            <td class=" border-top border-right fz-6  text-center fb">IMPORTE A CARGO<br>DEL BENEFICIARIO</td>
        </tr>
        <tr>
            <td class="border-left border-right fz-7 " rowspan="2" style="width: 233px">{{ $rp->laboratorio->nombre }} {{ $rp->laboratorio->presentacion }} {{ $rp->laboratorio->laboratorio }}</td>
            <td class=" border-top border-bottom border-right fz-7 text-center">en Letras</td>
            <td class=" border-top border-bottom border-right fz-7 text-center">en Números</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
        </tr>
        <tr>
            <td class="border-right fz-7 text-center" style="height: 50px;"></td>
            <td class="border-right fz-7 text-center">{{ $rp->cantidad_solicita }}</td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
        </tr>
    </table>
     @php
         $x++;
     @endphp
    @endforeach

    @if ( count($datos->detalle) == 0 || count($datos->detalle) == 1)
        @php
            $i = 2;
            $i = $i - $x;
        @endphp
        @for ($z = 0; $z <= $i; $z++)
            <table id="table">
        <tr>
            <td class="border-left border-top border-right fz-7 fb " style="width: 233px" rowspan="3">RP{{ $x }}.</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 128px" colspan="2">Cant. de unidades</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 55px">Precio Unitario</td>
            <td class="border-top border-right fz-6 text-center fb" style="width: 55px">TOTAL</td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 55px">DTO. % </td>
            <td class=" border-top border-right fz-6  text-center fb" style="width: 65px">MONTO FIJO<br>O.S.P.F</td>
            <td class=" border-top border-right fz-6  text-center fb">IMPORTE A CARGO<br>DEL BENEFICIARIO</td>
        </tr>
        <tr>
            <td class=" border-top border-bottom border-right fz-7 text-center">en Letras</td>
            <td class=" border-top border-bottom border-right fz-7 text-center">en Números</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
            <td class=" border-top border-right fz-7 text-left">$</td>
        </tr>
        <tr>
            <td class="border-right fz-7 text-center" style="height: 50px;"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
            <td class="border-right fz-7 text-center"></td>
        </tr>
        </table>
        @php
         $x++;
        @endphp
        @endfor


    @endif

    <table id="table">
        <tr>
            <td class="border-left border-top border-right fz-7 text-left fb" rowspan="4" style="width: 426px">
                <p>FECHA DE PRESCRIPCIÓN,<br>
                    FIRMA, SELLO C/NRO. MATRÍCULA<br>
                    Y ACLARACIÓN DEL PROFESIONAL</p>
            </td>
            <td class="border-right border-top fz-7 text-center fb" style="width: 164px">Total Receta</td>
            <td class="border-right border-top fz-7 text-center fb" style="width: 164px">A Cargo del Beneficiario</td>
        </tr>
        <tr>
            <td class="border-right border-bottom fz-7 text-center" style="height: 30px"></td>
            <td class="border-right border-bottom fz-7 text-center"></td>
        </tr>
        <tr>
            <td class="border-right fb fz-7 text-left" colspan="2">A Cargo de la Obra Social</td>
        </tr>
        <tr>
            <td colspan="2" class="border-right fz-7 text-left" style="height: 50px;"></td>
        </tr>
    </table>
    <table id="table">
        <tr>
            <td class="border-right border-left border-top fb fz-7 text-center">CERTIFICO LA ENTREGA DE MEDICAMENTOS</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 160px;">ROTULO 1</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 165px;">ROTULO 2</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 160px;">ROTULO 3</td>
        </tr>
        <tr>
            <td class="border-right border-left  fz-7 text-center" style="height: 50px"></td>

        </tr>
        <tr>
            <td class="border-right border-left fb  fz-7 text-center">FIRMA FARMACÉUTICO, SELLO FARMACIA</td>
        </tr>
        <tr></tr>
    </table>
    <table id="table">
        <tr>
            <td class="border-right border-left border-top fb fz-7 text-center">FIRMA Y ACLARACIÓN DEL AFILIADO</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 160px;">ROTULO 4</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 165px;">ROTULO 5</td>
            <td class="border-right border-top fz-7 text-center fb" rowspan="3" style="width: 160px;">ROTULO 6</td>
        </tr>
        <tr>
            <td class="border-right border-left  fz-7 text-center" style="height: 50px"></td>

        </tr>
        <tr>
            <td class="border-right border-left fb  fz-7 text-left">DOMICILIO: <br>TELÉFONO: </td>
        </tr>
        <tr></tr>
    </table>

    <table id="table">
        <tr>
            <td class="border-right border-left border-top  fz-9 text-left fb" style="width: 650px"><p>SOLO PARA AUTORIZACIÓN FUERA DE VADEMECUM<br> POR O.S.P.F. SEDE CENTRAL</p></td>
            <td class="border-right border-top  fz-9 text-left fb" style="width: 500px; height: 30px;"><p>NORMAS DE PRESCRIPCIÓN Y FACTURACIÓN</p></td>
        </tr>
        <tr>
            <td class="border-right border-left fz-9 text-left"></td>
            <td class="border-right   fz-9 text-left" style="height: 70px;">
                <b>La presente receta tendrá una validez de 30 días a partir de la fecha de prescripción médica.</b>
            </td>
        </tr>
         <tr>
            <td class="border-right border-left fz-9 text-right fb"> FIRMA Y SELLO</td>
            <td class="border-right border-left fz-9 text-left fb" style="height: 40px;">

            </td>
        </tr>
    </table>
    <table id="table">
        <tr>
            <td class="  border-left border-bottom border-top fz-9 text-left"><b>Delegación: {{ is_null($datos->usuario->cod_sindicato) ? $datos->afiliado->nombre_filial : $datos->usuario->filial->nombre_sindicato }}</b></td>
            <td class="  border-bottom border-top fz-9 text-right"><b>Importante: este Recetario vence el</b></td>
            <td class="border-right    border-bottom border-top fz-9 text-left"><b>{{ \Carbon\Carbon::parse($datos->fecha_vencimiento)->format('d/m/Y') }}</b></td>
        </tr>
    </table>
</body>
</html>
