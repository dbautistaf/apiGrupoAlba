<?php

namespace App\Http\Controllers\procesos;

use App\Models\AuditarPrestacionesPracticaLaboratorioEntity;
use App\Models\DetalleRecetarioEntity;
use App\Models\RecetariosEntity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditarRecetariosController extends Controller
{
    public function postAuditar(Request $request)
    {
        DB::beginTransaction();
        try {
            $fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
            $user = Auth::user();
            $detalle  = json_decode($request->detalle);

            $autorizados = 0;
            $noAutorizados = 0;
            $cod_prestacion = 0;
            $totalItems = 0;

            foreach ($detalle as $key) {
                if ($key->estado_autoriza) {
                    if (empty($key->cod_tipo_troquel)) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Se solicita que indique el tipo de cobertura para la prestación <b>" . $key->laboratorio . "</b>"
                        ], 409);
                    }

                    $detalle = DetalleRecetarioEntity::find($key->cod_detalle_receta);
                    $detalle->cod_tipo_troquel = $key->cod_tipo_troquel;
                    $detalle->cantidad_autoriza = $key->cantidad_autoriza;
                    $detalle->estado_autoriza = ($key->estado_autoriza ? 'SI' : 'NO');
                    $detalle->update();
                    $autorizados++;
                    $cod_prestacion = $detalle->cod_receta;

                    AuditarPrestacionesPracticaLaboratorioEntity::create([
                        'fecha_autorizacion' => $fechaActual,
                        'cod_usuario_audita' => $user->cod_usuario,
                        'observaciones' =>  $key->observacion_rechazo,
                        'cod_tipo_rechazo' => null,
                        'cod_recetario' => $key->cod_detalle_receta,
                        'estado_autoriza' => ($key->estado_autoriza ? 'SI' : 'NO'),
                    ]);

                } else {
                    if (empty($key->observacion_rechazo)) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Se solicita que indique el motivo del rechazo para la prestación <b>" . $key->laboratorio . "</b>"
                        ], 409);
                    }

                    $detalle = DetalleRecetarioEntity::find($key->cod_detalle_receta);
                    $detalle->cantidad_autoriza = $key->cantidad_autoriza;
                    $detalle->estado_autoriza = ($key->estado_autoriza ? 'SI' : 'NO');
                    $detalle->update();
                    $noAutorizados++;

                    AuditarPrestacionesPracticaLaboratorioEntity::create([
                        'fecha_autorizacion' => $fechaActual,
                        'cod_usuario_audita' => $user->cod_usuario,
                        'observaciones' =>  $key->observacion_rechazo,
                        'cod_tipo_rechazo' => null,
                        'cod_recetario' => $key->cod_detalle_receta,
                        'estado_autoriza' => ($key->estado_autoriza ? 'SI' : 'NO'),
                    ]);

                    $cod_prestacion = $detalle->cod_receta;
                }
                $totalItems++;
            }


            $prestacionLab = RecetariosEntity::find($cod_prestacion);
            if ($autorizados == $totalItems) {
                $prestacionLab->cod_tipo_estado = 1;
            } else if ($noAutorizados == $totalItems) {
                $prestacionLab->cod_tipo_estado = 3;
            } else {
                $prestacionLab->cod_tipo_estado = 6;
            }
            $prestacionLab->update();

            DB::commit();
            return response()->json(["message" => "Prestación auditada correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
