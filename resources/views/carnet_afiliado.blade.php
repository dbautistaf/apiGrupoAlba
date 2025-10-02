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
        }

        .contenedor {
            position: absolute;
            display: inline-block;
            text-align: center;
            margin-left: 10px;
        }

        .img {
            position: absolute;
        }

        .img img {
            position: relative;
            max-width: 750px;
            max-height: 500px;
        }

        .datos {
            position: absolute;
            margin-top: 3.5cm;
            float: left;
            padding-left: 5cm;
            width: 800px;
            text-align: left;
            color: #fff;
        }

        .nombre,
        .filial,
        .cuil,
        .plan {
            font-size: 20px;
            text-transform: uppercase;
            text-align: left;
            margin-top: 0.7cm;
            margin-left: -4cm;
        }

        .fecha .fecha_inicio {
            margin-left: -1.5cm !important;
            font-size: 20px;
            margin-top: 0.5cm;
        }

        .fecha .fecha_fin {
            margin-left: 5.5cm !important;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        $tipoPrincipal = $plan[0]->detalleplan[0]->addplan->tipo ?? null;
    @endphp
    @foreach ($data as $padron)
        <div class="contenedor">
            <div class="img">
                @if ($padron->id_locatario == 1)
                    <img src="{{ storage_path('app/public/images/BONSALUD.png') }}">
                @elseif ($padron->id_locatario == 2)
                    <img src="{{ storage_path('app/public/images/SEMBRAR.png') }}">
                @elseif ($padron->id_locatario == 3)
                    <img src="{{ storage_path('app/public/images/CREDENCIAL_BENE.png') }}">
                @else
                    <h1>No tiene un modelo de carnet definido</h1>
                @endif
                <div class="datos">
                    <p class="nombre">APELLIDOS Y NOMBRES:<b> {{ $padron->apellidos . ' ' . $padron->nombre }} </b></p>
                    <p class="filial">FILIACIÓN:<b class="parentezco">
                            {{ $padron['tipoParentesco']['parentesco'] ?? 'Titular' }} </b></p>
                    <p class="cuil">N° DE AFILIADO:<b> {{ $padron->dni }} </b></p>
                    <!-- <p class="plan">TIPO PLAN:<b> {{ $padron->detalleplan[0]->addplan->tipo ?? $tipoPrincipal }} </b>  -->
                    <p class="plan">TIPO PLAN:<b> PLAN ÚNICO </b>
                    <p class="plan">OBRA SOCIAL:<b> {{ $padron->origen->detalle_comercial_origen ?? '' }} </b>
                    </p>

                    <div class="fecha">
                        <p class="text fecha_inicio">VÁLIDO DESDE <b>{{ date('d/m/y', strtotime($f_inicio)) }}
                            </b>VÁLIDO HASTA <b>{{ date('d/m/y', strtotime($f_fin)) }}</b></p>
                    </div>
                </div>

            </div>
        </div>
        </div>
        @if (!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>

</html>
