<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mesa entrada</title>
    <style>
        * {
            margin: 4px;
            box-sizing: 0;
            font-family: Verdana, Geneva, Tahoma, sans-serif;
            font-size: 13.5px;
        }

        .contenedor {
            position: absolute;
            display: inline-block;
            text-align: center;
            margin-left: 10px;
        }
        .img img {
            margin-top: 20px;
            position: relative;
            max-width: 300px;
            max-height: 200px;
            border: #024d05 solid 2px;
        }

        .datos {
            position: absolute;
            margin-top: 0.5cm;
            float: left;
            padding-left: 1cm;
            width: 650px;
            text-align: left;
            color: #000;
        }

        .datos p{
            line-height: 2;
        }
        
        .baja{
            position: absolute;
            padding-left: 4cm;
            width: 300px;
        }
        .titulo{
            padding-top:20px ;
            font-size: 0.7cm;
        }
        .footer{
            position: relative;
            margin-top: 6cm;
        }
        .dni, .fecha, .firma{
            position: absolute;
        }
        .firma{
            padding-left: 1cm;
        }
        .dni{
            padding-left: 9.5cm;
        }
        .fecha{
            padding-left: 15cm;
        }
    </style>
</head>

<body>
    @php
        use Carbon\Carbon;
        Carbon::setLocale('es');

        $fechaFormateada = Carbon::now()->translatedFormat('d \d\e F \d\e Y');
        $tipoPrincipal = $plan[0]->detalleplan[0]->addplan->tipo ?? null;
    @endphp
    <div class="contenedor">
        <div class="img">
                @if ($padron->id_locatario == 1)
                    <img src="{{ storage_path('app/public/images/bon_baja.jpeg') }}">
                @elseif ($padron->id_locatario == 2)
                    <img src="{{ storage_path('app/public/images/sembrar_baja.jpeg') }}">
                @elseif ($padron->id_locatario == 3)
                    <img src="{{ storage_path('app/public/images/bene_baja.jpeg') }}">
                @else
                    <img src="{{ storage_path('app/public/images/alba.png') }}">
                @endif
            <p class="baja">CABA,  {{ $fechaFormateada }}</p>
            <p class="baja titulo">Baja de Afiliación</p>
            <div class="datos">
                <p>Se deja constancia que el/la Sr./Sra. {{ $padron->apellidos . ' ' . $padron->nombre }}, DNI Nº {{$padron->dni}}, ha
                    sido dado/a de baja como afiliado/a de esta Obra Social a partir del día {{ date('d/m/y', strtotime($padron->fe_baja)) }}, no
                    correspondiéndole la percepción de las prestaciones brindadas por la misma desde dicha fecha.<br><br>
                    La presente se emite a solicitud del interesado, a los fines que estime corresponder.                     
                </p>
            </div>
            <div class="footer">
                <div class="firma">
                     <p >____________________________________</p><p>Firma y Aclaracion del Beneficiario</p>
                </div>
                <div class="dni">
                    <p>____________________</p><p>DNI</p>
                </div>
               <div class="fecha">
                   <p>____________________</p><p>Fecha</p>
               </div>
            </div>
        </div>
    </div>
</body>

</html>
