<?php

namespace App\Http\Controllers;

use App\Models\AdhesionAfipModelo;
use App\Models\AltasMonotributoModelo;
use App\Models\AltasRegimenGeneralModelo;
use App\Models\BajaAutomaticaAfipModelo;
use App\Models\BajasMonotributoModelo;
use App\Models\BajasRegimenGeneralModelo;
use App\Models\SuperPadronModelo;
use App\Models\DesempleoModelo;
use App\Models\EfectoresSocialesModelo;
use App\Models\ExpedientesModelo;
use App\Models\FamiliaresMonotributoModelo;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class SuperPadronController extends Controller
{

    //listar super padron     
    public function getListSuperPadron(Request $request)
    {
        if ($request->nombre == '') {
            if ($request->tipo == '1' && $request->periodo == '') {
                $query =  SuperPadronModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2' && $request->periodo == '') {
                $query =  DesempleoModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3' && $request->periodo == '') {
                $query =  AdhesionAfipModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4' && $request->periodo == '') {
                $query =  BajaAutomaticaAfipModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5' && $request->periodo == '') {
                $query =  FamiliaresMonotributoModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6' && $request->periodo == '') {
                $query =  EfectoresSocialesModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7' && $request->periodo == '') {
                $query =  AltasRegimenGeneralModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8' && $request->periodo == '') {
                $query =  BajasRegimenGeneralModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9' && $request->periodo == '') {
                $query =  AltasMonotributoModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10' && $request->periodo == '') {
                $query =  BajasMonotributoModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11' && $request->periodo == '') {
                $query =  ExpedientesModelo::get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '1' && $request->periodo != '') {
                $query =  SuperPadronModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2' && $request->periodo != '') {
                $query =  DesempleoModelo::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3' && $request->periodo != '') {
                $query =  AdhesionAfipModelo::where('periodo_import', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4' && $request->periodo != '') {
                $query =  BajaAutomaticaAfipModelo::where('periodo_import', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5' && $request->periodo != '') {
                $query =  FamiliaresMonotributoModelo::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6' && $request->periodo != '') {
                $query =  EfectoresSocialesModelo::where('periodo_importacion', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7' && $request->periodo != '') {
                $query =  AltasRegimenGeneralModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8' && $request->periodo != '') {
                $query =  BajasRegimenGeneralModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9' && $request->periodo != '') {
                $query =  AltasMonotributoModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10' && $request->periodo != '') {
                $query =  BajasMonotributoModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11' && $request->periodo != '') {
                $query =  ExpedientesModelo::where('periodo', $request->periodo)->get();
                return response()->json($query, 200);
            }
        } else {
            if ($request->tipo == '1') {
                $query =  SuperPadronModelo::where('dni', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '2') {
                $query =  DesempleoModelo::where('nro_documento', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '3') {
                $query =  AdhesionAfipModelo::where('cuit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '4') {
                $query =  BajaAutomaticaAfipModelo::where('cuit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '5') {
                $query =  FamiliaresMonotributoModelo::where('nro_documento_fam', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres_fam', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '6') {
                $query =  EfectoresSocialesModelo::where('cuit_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres_efector', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '7') {
                $query =  AltasRegimenGeneralModelo::where('cuil_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '8') {
                $query =  BajasRegimenGeneralModelo::where('cuil_titular', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '9') {
                $query =  AltasMonotributoModelo::where('cuil', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '10') {
                $query =  BajasMonotributoModelo::where('cuil', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            } elseif ($request->tipo == '11') {
                $query =  ExpedientesModelo::where('cuil_tit', 'LIKE', "$request->nombre%")
                    ->orWhere('nombres', 'LIKE', "$request->nombre%")->get();
                return response()->json($query, 200);
            }
        }
    }

    public function saveSuperintendencia(Request $request)
    {
        set_time_limit(600);
        $user = Auth::user();
        $now = new \DateTime();
        $archivo = $request->file('file');
        if ($archivo) {
            $lineas = explode("\n", $archivo->get());
            if ($request->tipoArchivo == '1') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = SuperPadronModelo::where('dni', trim($campos[6]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            SuperPadronModelo::create([
                                'rnos' => trim($campos[0]),
                                'cuit' => trim($campos[1]),
                                'cuil_tit' => trim($campos[2]),
                                'parentesco' => trim($campos[3]),
                                'cuil_benef' => trim($campos[4]),
                                'tipo_doc' => trim($campos[5]),
                                'dni' => trim($campos[6]),
                                'nombres' => trim($campos[7]),
                                'sexo' => trim($campos[8]),
                                'estado_civi' => trim($campos[9]),
                                'fe_nac' => trim($campos[10]),
                                'nacionalidad' => trim($campos[11]),
                                'calle' => trim($campos[12]),
                                'numero' => trim($campos[13]),
                                'piso' => trim($campos[14]),
                                'depto' => trim($campos[15]),
                                'localidad' => trim($campos[16]),
                                'cp' => trim($campos[17]),
                                'id_prov' => trim($campos[18]),
                                'sd2' => trim($campos[19]),
                                'telefono' => trim($campos[20]),
                                'sd3' => trim($campos[21]),
                                'incapacidad' => trim($campos[22]),
                                'sd5' => trim($campos[23]),
                                'fe_alta' => trim($campos[24]),
                                'fe_novedad' => trim($campos[25]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '2') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = DesempleoModelo::where('nro_documento', trim($campos[4]))
                            ->where('periodo_importacion', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            DesempleoModelo::create([
                                'clave_desempleo' => trim($campos[0]),
                                'marca_fin_pago' => trim($campos[1]),
                                'parentesco' => trim($campos[2]),
                                'tipo_documento' => trim($campos[3]),
                                'nro_documento' => trim($campos[4]),
                                'provincia' => trim($campos[5]),
                                'cuil' => trim($campos[6]),
                                'fecha_nacimiento' => trim($campos[7]),
                                'nombres' => trim($campos[8]),
                                'fecha_vigencia' => trim($campos[9]),
                                'sexo' => trim($campos[10]),
                                'fecha_inicio_relacion' => trim($campos[11]),
                                'fecha_cese' => trim($campos[12]),
                                'rnos' => trim($campos[13]),
                                'fecha_proceso' => trim($campos[14]),
                                'cuil_titular' => trim($campos[15]),
                                'periodo_importacion' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '3') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = AdhesionAfipModelo::where('cuil_tit', trim($campos[0]))
                            ->where('periodo_import', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            AdhesionAfipModelo::create([
                                'cuil_tit' => trim($campos[0]),
                                'rnos' => trim($campos[1]),
                                'periodo' => trim($campos[2]),
                                'cuit' => trim($campos[3]),
                                'nombres' => trim($campos[4]),
                                'calle' => trim($campos[5]),
                                'numero' => trim($campos[6]),
                                'piso' => trim($campos[7]),
                                'depto' => trim($campos[8]),
                                'localidad' => trim($campos[9]),
                                'periodo_import' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '4') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = BajaAutomaticaAfipModelo::where('cuil_tit', trim($campos[0]))
                            ->where('periodo_import', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            BajaAutomaticaAfipModelo::create([
                                'cuil_tit' => trim($campos[0]),
                                'rnos' => trim($campos[1]),
                                'periodo' => trim($campos[2]),
                                'cuit' => trim($campos[3]),
                                'nombres' => trim($campos[4]),
                                'calle' => trim($campos[5]),
                                'numero' => trim($campos[6]),
                                'piso' => trim($campos[7]),
                                'depto' => trim($campos[8]),
                                'localidad' => trim($campos[9]),
                                'cp' => trim($campos[10]),
                                'provincia' => trim($campos[11]),
                                'categoria' => trim($campos[12]),
                                'periodo_import' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '5') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = FamiliaresMonotributoModelo::where('nro_documento_fam', trim($campos[3]))
                            ->where('periodo_importacion', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            $textoUtf8 = mb_convert_encoding($campos[4], 'UTF-8', 'ISO-8859-1');
                            FamiliaresMonotributoModelo::create([
                                'obra_social' => trim($campos[0]),
                                'cuit_titular' => trim($campos[1]),
                                'tipo_documento_fam' => trim($campos[2]),
                                'nro_documento_fam' => trim($campos[3]),
                                'apellido_fam' => trim($textoUtf8),
                                'nombres_fam' => trim($campos[5]),
                                'parentesco_fam' => trim($campos[6]),
                                'fecha_alta_fam' => trim($campos[7]),
                                'id_usuario' => $user->cod_usuario,
                                'periodo_importacion' => $request->periodo,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '6') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = EfectoresSocialesModelo::where('cuit_titular', trim($campos[0]))
                            ->where('periodo_importacion', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            EfectoresSocialesModelo::create([
                                'cuit_titular' => trim($campos[0]),
                                'obra_social' => trim($campos[1]),
                                'nombres_efector' => trim($campos[2]),
                                'calle' => trim($campos[3]),
                                'numero' => trim($campos[4]),
                                'piso' => trim($campos[5]),
                                'departamento' => trim($campos[6]),
                                'localidad' => trim($campos[7]),
                                'codigo_postal' => trim($campos[8]),
                                'provincia' => trim($campos[9]),
                                'id_usuario' => $user->cod_usuario,
                                'periodo_importacion' => $request->periodo,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '7') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = AltasRegimenGeneralModelo::where('cuil_titular', trim($campos[0]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            AltasRegimenGeneralModelo::create([
                                'cuil_titular' => trim($campos[2]),
                                'nombres' => trim($campos[3]),
                                'fecha_vigencia' => trim($campos[4]),
                                'telefono' => trim($campos[5]),
                                'email' => trim($campos[6]),
                                'codigo_postal' => trim($campos[7]),
                                'localidad' => trim($campos[6]),
                                'provincia' => trim($campos[7]),
                                'obra_social_origen' => trim($campos[8]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '8') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = BajasRegimenGeneralModelo::where('cuil_titular', trim($campos[0]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            BajasRegimenGeneralModelo::create([
                                'cuil_titular' => trim($campos[0]),
                                'nombres' => trim($campos[1]),
                                'fecha_vigencia' => trim($campos[2]),
                                'telefono' => trim($campos[3]),
                                'email' => trim($campos[4]),
                                'codigo_postal' => trim($campos[5]),
                                'localidad' => trim($campos[6]),
                                'provincia' => trim($campos[7]),
                                'obra_social_origen' => trim($campos[8]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '9') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = AltasMonotributoModelo::where('cuil', trim($campos[0]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            AltasMonotributoModelo::create([
                                'tipo' => trim($campos[0]),
                                'formulario' => trim($campos[1]),
                                'cuil' => trim($campos[2]),
                                'nombres' => trim($campos[3]),
                                'periodo_vigencia' => trim($campos[4]),
                                'telefono' => trim($campos[5]),
                                'email' => trim($campos[6]),
                                'codigo_postal' => trim($campos[7]),
                                'localidad' => trim($campos[8]),
                                'provincia' => trim($campos[9]),
                                'obra_social_origen' => trim($campos[10]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '10') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = BajasMonotributoModelo::where('cuil', trim($campos[0]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            BajasMonotributoModelo::create([
                                'tipo' => trim($campos[0]),
                                'formulario' => trim($campos[1]),
                                'cuil' => trim($campos[2]),
                                'nombres' => trim($campos[3]),
                                'periodo_vigencia' => trim($campos[4]),
                                'telefono' => trim($campos[5]),
                                'email' => trim($campos[6]),
                                'codigo_postal' => trim($campos[7]),
                                'localidad' => trim($campos[8]),
                                'provincia' => trim($campos[9]),
                                'obra_social_origen' => trim($campos[10]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            } elseif ($request->tipoArchivo == '11') {
                foreach ($lineas as $linea) {
                    $campos = explode('|', $linea);
                    if (isset($campos[1])) {
                        $query = ExpedientesModelo::where('cuil_tit', trim($campos[1]))
                            ->where('periodo', '=', $request->periodo)
                            ->first();
                        if (!$query) {
                            ExpedientesModelo::create([
                                'rnos' => trim($campos[0]),
                                'cuil_tit' => trim($campos[1]),
                                'nombres' => trim($campos[2]),
                                'cod_mov' => trim($campos[3]),
                                'movimiento' => trim($campos[4]),
                                'fecha_vigencia' => trim($campos[5]),
                                'expediente' => trim($campos[6]),
                                'año_expediente' => trim($campos[7]),
                                'tipo_disposicion' => trim($campos[8]),
                                'disposicion' => trim($campos[9]),
                                'periodo' => $request->periodo,
                                'id_usuario' => $user->cod_usuario,
                                'fecha_importacion' => $now->format('Y-m-d'),
                            ]);
                        }
                    }
                }
            }
            return response()->json(['message' => 'Archivo registrado correctamente'], 200);
        }
    }
}
