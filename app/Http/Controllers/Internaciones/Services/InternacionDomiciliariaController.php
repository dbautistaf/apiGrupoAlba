<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\InternacionDomiciliariaRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InternacionDomiciliariaController extends Controller
{
    public function getAgregarServicio(Request $request, InternacionDomiciliariaRepository $repo)
    {
        if (!is_null($request->id_servicio)) {
            $repo->findByUpdateService($request);
            return response()->json(['message' => 'Servicio actualizado correctamente']);
        } else {
            $repo->findByAddService($request);
            return response()->json(['message' => 'Servicio agregado correctamente']);
        }
    }

    public function getEliminarServicio(Request $request, InternacionDomiciliariaRepository $repo)
    {
        $repo->findByDeleteServiceId($request->id_servicio);
        return response()->json(['message' => 'Servicio eliminado correctamente']);
    }

    public function getCargarSolicitud(Request $request, InternacionDomiciliariaRepository $repo, ManejadorDeArchivosUtils $storagaFile)
    {
        try {
            DB::beginTransaction();
            $internacion = json_decode($request->datos);
            if (!is_null($internacion->id_internacion_domiciliaria)) {
                $inter = $repo->findByUpdateInternacionDomiciliaria($internacion);
                $repo->findByInternacionDomiciliariaDetalle($internacion->detalle, $inter);
            } else {
                $inter = $repo->findBySaveInternacionDomiciliaria($internacion);
                $repo->findByInternacionDomiciliariaDetalle($internacion->detalle, $inter);
                if ($request->hasFile('archivos')) {
                    $archivosAdjuntos = $storagaFile->findByCargaMasivaArchivos("INTERNACION_" . $inter->dni_afiliado, 'Internacion_domiciliaria', $request);
                    $repo->findBySaveInternacionDomiciliariaFile($archivosAdjuntos, $inter->id_internacion_domiciliaria);
                }
            }
            DB::commit();
            return response()->json(['message' => 'Solicitud procesada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getEliminarSolicitud(Request $request, InternacionDomiciliariaRepository $repo)
    {
        $repo->findByDeleteInternacionDomiciliariaDetalleId($request->id_servicio);
        return response()->json(['message' => 'Solicitud eliminada correctamente']);
    }

    public function getFinalizar(Request $request, InternacionDomiciliariaRepository $repo)
    {
        $repo->findByFinalizarId($request);
        return response()->json(['message' => 'Solicitud finalizada correctamente']);
    }
}
