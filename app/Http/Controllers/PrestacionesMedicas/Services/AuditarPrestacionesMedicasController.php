<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\AuditarPrestacionesMedicasRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuditarPrestacionesMedicasController extends Controller
{

    public function getAuditarPrestacionMedica(AuditarPrestacionesMedicasRepository $repoAuditar, Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle  = json_decode($request->detalle);
            $observacion = json_decode($request->observacion);
            $autorizados = 0;
            $noAutorizados = 0;
            $sumaTotalAuditado = 0;
            $cod_prestacion = 0;
            $totalItems = 0;

            foreach ($detalle as $key) {
                if ($key->estado_autoriza) {
                    $nuevoMonto = $repoAuditar->findByAutorizarItemDetallePrestacion($key);
                    $autorizados++;
                    $sumaTotalAuditado += $nuevoMonto;
                } else {
                    if (empty($key->cot_tipo_rechazo)) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Se solicita que indique el motivo del rechazo para la prestación <b>" . $key->descripcion_practica . "</b>"
                        ], 409);
                    }
                    $repoAuditar->findByDenegarAutorizacionItemDetallePrestacion($key);
                    $noAutorizados++;
                }

                $cod_prestacion = $key->cod_prestacion;
                $repoAuditar->finByRegistrarAuditoria($key, $observacion);
                $totalItems++;
            }
            $repoAuditar->findByUpdatePrestacionMedica($cod_prestacion, $autorizados, $totalItems, $noAutorizados, $sumaTotalAuditado);
            DB::commit();
            return response()->json(["message" => "Prestación auditada correctamente"], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getListarHistorialAutorizacionesAfiliado(AuditarPrestacionesMedicasRepository $repoAuditar, Request $request)
    {

        return response()->json($repoAuditar->findByListAfiliado($request));
    }
}
