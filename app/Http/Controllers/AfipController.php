<?php

namespace App\Http\Controllers;

use App\Exports\DeclaracionesJuradas;
use App\Exports\Transferencias;
use App\Models\arca\DomicilioExplotacionModel;
use App\Models\arca\RelacionLaboralModel;
use App\Models\DdjjEmpresasModelo;
use App\Models\DeclaracionesJuradasModelo;
use App\Models\DetalleAfipHeaderModelo;
use App\Models\SubsidioSanoModelo;
use App\Models\SubsidioSuma70Modelo;
use App\Models\SubsidioSumaModelo;
use App\Models\SubsidioSumarteModelo;
use App\Models\TransferenciasModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AfipController extends Controller
{
    //
    public function listAfip(Request $request)
    {

        $query = [];
        switch ($request->tipo) {
            case '1':
                $model = DeclaracionesJuradasModelo::with('PadronAfil', 'Empresa');
                $periodField = 'periodo_ddjj';
                $fechaField = 'fecha_proceso';
                $cuilField = 'cuil';
                $cuitField = 'cuit';
                break;
            case '2':
                $model = TransferenciasModelo::with('PadronAfil', 'Empresa');
                $periodField = 'periodo_tranf';
                $fechaField = 'fecha_proceso';
                $cuilField = 'cuitapo';
                $cuitField = 'cuitcont';
                break;
            case '3':
                return response()->json(['message' => 'La estructura de este archivo aún no está definida'], 500);
            case '4':
                $model = SubsidioSumaModelo::query();
                $periodField = 'periodo_subsidio_suma';
                $fechaField = 'fecha_proceso';
                break;
            case '5':
                $model = SubsidioSumarteModelo::query();
                $periodField = 'periodo_subsidio_sumarte';
                $fechaField = 'fecha_proceso';
                break;
            case '6':
                $model = SubsidioSuma70Modelo::query();
                $periodField = 'periodo_subsidio_sumarte';
                $fechaField = 'fecha_proceso';
                break;
            case '7':
                $model = SubsidioSanoModelo::query();
                $periodField = 'periodo_subsidio_sano';
                $fechaField = 'fecha_proceso';
                $cuilField = 'cuil';
                $cuitField = 'cuit';
                break;
            case '8':
                $model = DdjjEmpresasModelo::query();
                $periodField = 'periodo';
                $fechaField = 'fecha_proceso';
                $cuitField = 'cuit';
                break;
            case '9':
                $model = RelacionLaboralModel::query();
                $fechaField = 'fecha_proceso';
                $cuilField = 'cuil_empleado';
                $cuitField = 'cuit_empleador';
                break;
            case '10':
                $model = DomicilioExplotacionModel::with('Empresa');
                $fechaField = 'fecha_proceso';
                $cuitField = 'cuit_empleador';
                break;
            default:
                return response()->json(['message' => 'Tipo no válido'], 400);
        }
        $query = $model;
        if (!empty($request->desde) && empty($request->hasta)) {
            return response()->json(['message' => 'Falta el rango de fecha final'], 500);
        }

        if (empty($request->desde) && !empty($request->hasta)) {
            return response()->json(['message' => 'Falta el rango de fecha inicial'], 500);
        }

        if ($request->periodo == 'NINGUNA' && empty($request->desde) && empty($request->hasta) && empty($request->cuit) && empty($request->cuil) && empty($request->locatario)) {
            $query = $query->limit(50);
        }


        if ($request->periodo != 'NINGUNA') {
            $query = $query->where($periodField, $request->periodo);
        }

        if (!empty($request->desde) && !empty($request->hasta)) {
            $query = $query->whereBetween($fechaField, [$request->desde, $request->hasta]);
        }

        if (!empty($request->cuit) && $cuitField) {
            $query = $query->where($cuitField, 'LIKE', "$request->cuit%");
        }

        if (!empty($request->cuil) && $cuilField) {
            $query = $query->where($cuilField, 'LIKE', "$request->cuil%");
        }

        if (!empty($request->locatario)) {
            $query = $query->whereHas('PadronAfil', function ($q) use ($request) {
                $q->where('id_locatario', 'LIKE', "%$request->locatario%");
            });
        }

        if (!empty($request->unidad)) {
            $query = $query->whereHas('PadronAfil', function ($q) use ($request) {
                $q->where('id_unidad_negocio', 'LIKE', "%$request->unidad%");
            });
        }

        $result = $query->get();

        return response()->json($result, 200);
    }

    public function getListCuitFile(Request $request)
    {
        $query = '';
        if ($request->tipo == '1' && $request->cuit != '') {
            $query = DeclaracionesJuradasModelo::with('PadronAfil', 'Empresa')->where('cuit', 'LIKE', "$request->cuit%")->get();
        } elseif ($request->tipo == '2' && $request->cuit != '') {
            $query = TransferenciasModelo::with('PadronAfil', 'Empresa')->where('cuitcont', 'LIKE', "$request->cuit%")->get();
        } elseif ($request->tipo == '3' && $request->cuit != '') {
            return response()->json(['message' => 'La estructura de este archivo a un no esta definida'], 500);
        } elseif ($request->tipo == '7' && $request->cuit != '') {
            $query = SubsidioSanoModelo::where('cuit', 'LIKE', "$request->cuit%")->get();
        } elseif ($request->tipo == '8' && $request->cuit != '') {
            $query = DdjjEmpresasModelo::where('cuit', 'LIKE', "$request->cuit%")->get();
        } else {
            return response()->json(['message' => 'No existe un cuit en el tipo de archivo seleccionado'], 500);
        }
        return response()->json($query, 200);
    }

    public function getListCuilFile(Request $request)
    {
        $query = '';
        if ($request->tipo == '1' && $request->cuil != '') {
            $query = DeclaracionesJuradasModelo::with('PadronAfil', 'Empresa')->where('cuil', 'LIKE', "$request->cuil%")->get();
        } elseif ($request->tipo == '2' && $request->cuil != '') {
            $query = TransferenciasModelo::with('PadronAfil', 'Empresa')->where('cuitapo', 'LIKE', "$request->cuil%")->get();
        } elseif ($request->tipo == '3' && $request->cuil != '') {
            return response()->json(['message' => 'La estructura de este archivo a un no esta definida'], 500);
        } elseif ($request->tipo == '7' && $request->cuil != '') {
            $query = SubsidioSanoModelo::where('cuil', 'LIKE', "$request->cuil%")->get();
        } else {
            return response()->json(['message' => 'No hay un campo cuil en el tipo de archivo seleccionado'], 500);
        }
        return response()->json($query, 200);
    }

    public function getListNombreEmpresa(Request $request)
    {
        $query = '';
        $valorCampoPadron = $request->empresa;
        if ($request->tipo == '8' && $request->empresa != '') {
            $query = DdjjEmpresasModelo::where('nombre_empresa', 'LIKE', "$request->empresa%")->get();
        } elseif ($request->tipo == '1' && $request->empresa != '') {
            $query = DeclaracionesJuradasModelo::with('PadronAfil', 'Empresa')
                ->whereHas('Empresa', function ($queryEmpresa) use ($valorCampoPadron) {
                    $queryEmpresa->where('razon_social', 'LIKE', "$valorCampoPadron%");
                })->get();
        } elseif ($request->tipo == '2' && $request->empresa != '') {
            $query = TransferenciasModelo::with('PadronAfil', 'Empresa')
                ->whereHas('Empresa', function ($queryEmpresa) use ($valorCampoPadron) {
                    $queryEmpresa->where('razon_social', 'LIKE', "$valorCampoPadron%");
                })->get();
        } else {
            return response()->json(['message' => 'No hay un campo nombre de empresa en el tipo de archivo seleccionado'], 500);
        }
        return response()->json($query, 200);
    }

    public function getListFecha(Request $request)
    {
        $query = '';
        if ($request->tipo == '1' && $request->desde != '') {
            $query = DeclaracionesJuradasModelo::with('PadronAfil')->with('Empresa')->whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '2' && $request->desde != '') {
            $query = TransferenciasModelo::with('PadronAfil')->with('Empresa')->whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '3' && $request->desde != '') {
            return response()->json(['message' => 'La estructura de este archivo a un no esta definida'], 500);
        } elseif ($request->tipo == '4' && $request->desde != '') {
            $query = SubsidioSumaModelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '5' && $request->desde != '') {
            $query = SubsidioSumarteModelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '6' && $request->desde != '') {
            $query = SubsidioSuma70Modelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '7' && $request->desde != '') {
            $query = SubsidioSanoModelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        } elseif ($request->tipo == '8' && $request->desde != '') {
            $query = DdjjEmpresasModelo::whereBetween('fecha_proceso', [$request->desde, $request->hasta])->get();
        }
        return response()->json($query, 200);
    }



    public function saveAfip(Request $request)
    {
        set_time_limit(600);
        $user = Auth::user();
        $now = new \DateTime();
        $archivo = $request->file('file');
        if ($archivo) {
            // Dividir el contenido en líneas
            $lineas = explode("\n", $archivo->get());

            // Número máximo de caracteres por campo
            if ($request->tipoArchivo == '1') {
                $cantidadChar_por_campo = [6, 4, 11, 11, 14, 14, 2, 2, 2, 3, 2, 2, 3, 3, 3, 2, 11, 2, 14, 1, 12, 1, 1, 10, 10, 1, 11, 14, 14, 1, 6, 14, 2]; //declaraciones juradas
            } elseif ($request->tipoArchivo == '2') {
                $cantidadChar_por_campo = [4, 3, 15, 1, 10, 10, 11, 4, 15, 11, 3, 3, 2]; //transferencias
            } elseif ($request->tipoArchivo == '3') {
                return response()->json(['message' => 'La estructura de este archivo a un no esta definida'], 500);
            } elseif ($request->tipoArchivo == '4') {
                $cantidadChar_por_campo = [6, 6, 7, 15, 15, 15, 15, 15, 16, 15]; //subsidio suma   
            } elseif ($request->tipoArchivo == '5') {
                $cantidadChar_por_campo = [6, 6, 7, 91, 15, 26]; //subsidio sumarte                
            } elseif ($request->tipoArchivo == '6') {
                $cantidadChar_por_campo = [6, 6, 7, 91, 15, 26]; //subsidio suma70                
            } elseif ($request->tipoArchivo == '7') {
                $cantidadChar_por_campo = [2, 11, 11, 2, 4, 12, 12, 12, 12, 6, 1, 1, 1]; //subsidio sano                 
            } elseif ($request->tipoArchivo == '8') {
                $cantidadChar_por_campo = [11, 50, 20, 7, 5, 20, 3, 8, 6]; //ddjj empresas                
            } elseif ($request->tipoArchivo == '9') {
                $cantidadChar_por_campo = [11, 11, 55, 10, 10, 6, 20, 10, 1, 5, 20, 10, 1, 5, 3, 2, 2, 2, 5, 10, 1, 8, 2, 11, 1, 5, 6, 4, 10, 4, 1, 10, 3, 6, 10, 19]; //relacion laboral
            } elseif ($request->tipoArchivo == '10') {
                $cantidadChar_por_campo = [11, 2, 1, 60, 6, 5, 5, 5, 5, 8, 60, 2, 5, 6, 26, 43]; //Domicilio explotacion
            }

            try {

                DB::beginTransaction();
                //buscar header de txt 
                $header = str_replace(' ', '', $lineas[0]);
                $query = DetalleAfipHeaderModelo::where('header_txt', $header)->first();
                if (!$query) {
                    DetalleAfipHeaderModelo::create([
                        'header_txt' => $header,
                        'fecha_proceso' => $now->format('Y-m-d')
                    ]);
                } else {
                    return response()->json(['message' => 'El archivo seleccionado ya se guardo anteriormente'], 500);
                }

                // Recorrer a partir de la segunda línea
                for ($i = 1; $i < count($lineas); $i++) {
                    // Obtener la línea actual
                    $line = $lineas[$i];

                    $posicion = 0;
                    $files = [];
                    // Recorrer las diferentes cantidades de caracteres por fragmento
                    foreach ($cantidadChar_por_campo as $cantidad) {
                        $fragmento = substr($line, $posicion, $cantidad);
                        array_push($files, $fragmento);
                        $posicion += $cantidad;
                    }
                    //return response()->json(['message' => $files], 500);
                    if ($files[0] != '') {
                        if ($request->tipoArchivo == '1') {
                            if (substr($files[0], 0, -2) != 'TFOS') {
                                $remimpo = (substr($files[4], 0, -2) . '.' . substr($files[4], -2));
                                $imposad = (substr($files[5], 0, -2) . '.' . substr($files[5], -2));
                                $apadios = (substr($files[16], 0, -2) . '.' . substr($files[16], -2));
                                $rem5 = (substr($files[18], 0, -2) . '.' . substr($files[18], -2));
                                $excosapo = (substr($files[20], 0, -2) . '.' . substr($files[20], -2));

                                $remcont = (!empty($files[31]) && strlen(trim($files[31])) >= 2)
                                    ? (substr($files[31], 0, -2) . '.' . substr($files[31], -2))
                                    : '0.00';

                                $apobsoc = (!empty($files[26]) && strlen(trim($files[26])) >= 2)
                                    ? (substr($files[26], 0, -2) . '.' . substr($files[26], -2))
                                    : '0.00';

                                $conos = (!empty($files[27]) && strlen(trim($files[27])) >= 2)
                                    ? (substr($files[27], 0, -2) . '.' . substr($files[27], -2))
                                    : '0.00';

                                $remtot = (!empty($files[28]) && strlen(trim($files[28])) >= 2)
                                    ? (substr($files[28], 0, -2) . '.' . substr($files[28], -2))
                                    : '0.00';
                                DeclaracionesJuradasModelo::create([
                                    'codosoc' => $files[0],
                                    'periodo' => $files[1],
                                    'cuit' => $files[2],
                                    'cuil' => $files[3],
                                    'remimpo' => $remimpo,
                                    'imposad' => $imposad,
                                    'zona' => $files[6],
                                    'grpfam' => $files[7],
                                    'nogrpfam' => $files[8],
                                    'secoblig' => $files[9],
                                    'condicion' => $files[10],
                                    'situacion' => $files[11],
                                    'actividad' => $files[12],
                                    'modalidad' => $files[13],
                                    'ceros_demas' => $files[14],
                                    'codsini' => $files[15],
                                    'apadios' => $apadios,
                                    'version' => $files[17],
                                    'rem5' => $rem5,
                                    'esposa' => $files[19],
                                    'excosapo' => $excosapo,
                                    'indret' => $files[21],
                                    'indexccon' => $files[22],
                                    'fecpresent' => $files[23],
                                    'fecproc' => $files[24],
                                    'origrect' => $files[25],
                                    'apobsoc' => $apobsoc,
                                    'conos' => $conos,
                                    'remtot' => $remtot,
                                    'codosoc_inform' => $files[30],
                                    'rembase_cos' => $remcont,
                                    'release_ver' => $files[32],
                                    'periodo_ddjj' => $request->periodo,
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,

                                ]);
                            }
                        } elseif ($request->tipoArchivo == '2') {
                            if ($files[0] !== 'TNNO' && $files[0] !== 'TFTR') {
                                $importe = (substr($files[2], 0, -2) . '.' . substr($files[2], -2));
                                TransferenciasModelo::create([
                                    'organ' => $files[0],
                                    'codconc' => $files[1],
                                    'importe' => $importe,
                                    'inddbcr' => $files[3],
                                    'fecproc' => $files[4],
                                    'fecrec' => $files[5],
                                    'cuitcont' => $files[6],
                                    'periodo' => $files[7],
                                    'id_tranf' => $files[8],
                                    'cuitapo' => $files[9],
                                    'banco' => $files[10],
                                    'codsuc' => $files[11],
                                    'zona' => $files[12],
                                    'periodo_tranf' => $request->periodo,
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,
                                ]);
                            }
                        } elseif ($request->tipoArchivo == '3') {
                        } elseif ($request->tipoArchivo == '4') {
                            $importe = (substr($files[3], 0, -2) . '.' . substr($files[3], -2));
                            $capita = (substr($files[4], 0, -2) . '.' . substr($files[4], -2));
                            $art2_inca = (substr($files[5], 0, -2) . '.' . substr($files[5], -2));
                            $art2_incb = (substr($files[6], 0, -2) . '.' . substr($files[6], -2));
                            $rt2_incc = (substr($files[7], 0, -2) . '.' . substr($files[7], -2));
                            $art3_ajuste = (substr($files[8], 0, -2) . '.' . substr($files[8], -2));
                            $subsidio_total = (substr($files[9], 0, -2) . '.' . substr($files[9], -2));
                            SubsidioSumaModelo::create([
                                'cod_obra_soc' => $files[0],
                                'periodo' => $files[1],
                                'cant_benef' => $files[2],
                                'importe' => $files[3],
                                'capita' => $capita,
                                'art2_inca' => $art2_inca,
                                'art2_incb' => $art2_incb,
                                'art2_incc' => $rt2_incc,
                                'art3_ajuste' => $art3_ajuste,
                                'subsidio_total' => $subsidio_total,
                                'periodo_subsidio_suma' => $request->periodo,
                                'fecha_proceso' => $now->format('Y-m-d'),
                                'id_usuario' => $user->cod_usuario,
                            ]);
                        } elseif ($request->tipoArchivo == '5') {

                            return response()->json(['message' => $files], 200);
                            $subsidio_total = (substr($files[4], 0, -2) . '.' . substr($files[4], -2));
                            SubsidioSumarteModelo::create([
                                'cod_obra_soc' => $files[0],
                                'periodo' => $files[1],
                                'cant_benef' => $files[2],
                                'area_reser' => $files[3],
                                'subsidio_total' => $subsidio_total,
                                'periodo_subsidio_sumarte' => $request->periodo,
                                'fecha_proceso' => $now->format('Y-m-d'),
                                'id_usuario' => $user->cod_usuario,
                            ]);
                        } elseif ($request->tipoArchivo == '6') {
                            $subsidio_total = (substr($files[4], 0, -2) . '.' . substr($files[4], -2));
                            SubsidioSuma70Modelo::create([
                                'cod_obra_soc' => $files[0],
                                'periodo' => $files[1],
                                'cant_benef' => $files[2],
                                'area_reser' => $files[3],
                                'subsidio_total' => $subsidio_total,
                                'periodo_subsidio_suma70' => $request->periodo,
                                'fecha_proceso' => $now->format('Y-m-d'),
                                'id_usuario' => $user->cod_usuario,
                            ]);
                        } elseif ($request->tipoArchivo == '7') {
                            if ($files[0] != 'TR ') {
                                $remosimp = (substr($files[5], 0, -2) . '.' . substr($files[5], -2));
                                $apobsoc = (substr($files[6], 0, -2) . '.' . substr($files[6], -2));
                                $conosoc = (substr($files[7], 0, -2) . '.' . substr($files[7], -2));
                                $subsidio = (substr($files[8], 0, -2) . '.' . substr($files[8], -2));
                                SubsidioSanoModelo::create([
                                    'tipo_reg' => $files[0],
                                    'cuit' => $files[1],
                                    'cuil' => $files[2],
                                    'codosoc' => $files[3],
                                    'periodo' => $files[4],
                                    'remosimp' => $remosimp,
                                    'apobsoc' => $apobsoc,
                                    'conosoc' => $conosoc,
                                    'subsidio' => $subsidio,
                                    'obsocrel' => $files[9],
                                    'inpartot' => $files[10],
                                    'inddbcr' => $files[11],
                                    'motoexcep' => $files[12],
                                    'periodo_subsidio_sano' => $request->periodo,
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,
                                ]);
                            }
                        } elseif ($request->tipoArchivo == '8') {
                            if (substr($files[0], 0, -7) != 'TFOS') {
                                DdjjEmpresasModelo::create([
                                    'cuit' => $files[0],
                                    'nombre_empresa' => mb_convert_encoding($files[1], 'UTF-8', 'ISO-8859-1'),
                                    'calle' => mb_convert_encoding($files[2], 'UTF-8', 'ISO-8859-1'),
                                    'numero' => $files[3],
                                    'piso' => mb_convert_encoding($files[4], 'UTF-8', 'ISO-8859-1'),
                                    'localidad' => mb_convert_encoding($files[5], 'UTF-8', 'ISO-8859-1'),
                                    'cod_prov' => $files[6],
                                    'cp' => $files[7],
                                    'cod_os' => $files[8],
                                    'periodo' => $request->periodo,
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,
                                ]);
                            }
                        }elseif ($request->tipoArchivo == '9') {
                            if (substr($files[0], 0, -7) != 'TFAB') {
                                RelacionLaboralModel::create([
                                    'cuit_empleador' => $files[0],
                                    'cuil_empleado' => $files[1],
                                    'apellido_nombre' => $files[2],
                                    'fecha_inicio_relacion' => $files[3],
                                    'fecha_fin_relacion' => $files[4],
                                    'codigo_obra_social' => $files[5],
                                    'clave_alta_registro' => $files[6],
                                    'fecha_clave_alta' => $files[7],
                                    'separador1' => $files[8],
                                    'hora_clave_alta' => $files[9],
                                    'clave_baja_registro' => $files[10],
                                    'fecha_clave_baja' => $files[11],
                                    'separador2' => $files[12],
                                    'hora_clave_baja' => $files[13],
                                    'codigo_modalidad_contrato' => $files[14],
                                    'trabajador_agropecuario' => $files[15],
                                    'regimen_aportes' => $files[16],
                                    'codigo_situacion_baja' => $files[17],
                                    'filler1' => $files[18],
                                    'fecha_movimiento' => $files[19],
                                    'separador3' => $files[20],
                                    'hora_movimiento' => $files[21],
                                    'codigo_movimiento' => $files[22],
                                    'remuneracion_bruta' => $files[23],
                                    'codigo_modalidad_liquidacion' => $files[24],
                                    'codigo_sucursal_explotacion' => $files[25],
                                    'codigo_actividad' => $files[26],
                                    'codigo_puesto_desempenado' => $files[27],
                                    'fecha_telegrama_renuncia' => $files[28],
                                    'filler2' => $files[29],
                                    'marca_rectificacion' => $files[30],
                                    'numero_formulario_agropecuario' => $files[31],
                                    'tipo_servicio' => $files[32],
                                    'codigo_categoria_profesional' => $files[33],
                                    'cct' => $files[34],
                                    'area_reservada' => $files[35],
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,
                                ]);
                            }
                        } elseif ($request->tipoArchivo == '10') {
                            if (substr($files[0], 0, -7) != 'T000') {
                                DomicilioExplotacionModel::create([
                                    'cuit_empleador' => $files[0],
                                    'codigo_movimiento' => $files[1],
                                    'tipo_externo' => $files[2],
                                    'calle' => $files[3],
                                    'numero_puerta' => $files[4],
                                    'torre' => $files[5],
                                    'bloque' => $files[6],
                                    'piso' => $files[7],
                                    'departamento' => $files[8],
                                    'codigo_postal' => $files[9],
                                    'localidad' => $files[10],
                                    'provincia' => $files[11],
                                    'sucursal' => $files[12],
                                    'actividad' => $files[13],
                                    'fecha_hora_movimiento' => $files[14],
                                    'area_reservada' => $files[15],
                                    'fecha_proceso' => $now->format('Y-m-d'),
                                    'id_usuario' => $user->cod_usuario,
                                ]);
                            }
                        } else {
                            return response()->json(['message' => $files], 500);
                        }
                    }
                }
                DB::commit();
                //return response()->json(['message' => $files], 200);
                return response()->json(['message' => 'Archivo procesado correctamente'], 200);
            } catch (\Throwable $err) {
                //throw $th;

                // Si ocurre algún error, revierte la transacción
                DB::rollBack();
                //return response()->json(['message' => $files], 500);
                return response()->json(['message' => 'Error al ajecutar la carga de archivo, verifique archivo e intentelo nuevamente'], 500);
            }
        }
    }

    public function exportAfip(Request $request)
    {

        try {
            if ($request->tipo == '1') {
                return Excel::download(new DeclaracionesJuradas($request), 'DDJJ.xlsx');
            } elseif ($request->tipo == '2') {
                return Excel::download(new Transferencias($request), 'transfe.xlsx');
            } else {
                return response()->json(['error' => 'Tipo no válido'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
