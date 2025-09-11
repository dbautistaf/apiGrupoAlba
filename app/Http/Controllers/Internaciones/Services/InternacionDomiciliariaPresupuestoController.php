<?php

namespace App\Http\Controllers\Internaciones\Services;

use App\Http\Controllers\Internaciones\Repository\InternacionDomiciliariaPresupuestosRepository;
use App\Http\Controllers\Internaciones\Repository\InternacionDomiciliariaRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class InternacionDomiciliariaPresupuestoController extends Controller
{
    public function getCargarParticipantesPresupuestos(Request $request, InternacionDomiciliariaPresupuestosRepository $repo, InternacionDomiciliariaRepository $repoDom)
    {
        $repo->findBySolicitarPresupuestos($request->detalle, $request->id_internacion_domiciliaria);
        $repoDom->findByUpdateEstadoId($request->id_internacion_domiciliaria, 5);
        return response()->json(['message' => 'Prestadores cargados correctamente']);
    }

    public function getListarParticipantes(Request $request, InternacionDomiciliariaPresupuestosRepository $repo)
    {
        return response()->json($repo->findByListParticipantes($request->id));
    }

    public function getListarParticipantesDetalleServicios(Request $request, InternacionDomiciliariaPresupuestosRepository $repo)
    {
        $participantes = $repo->findByListParticipantes($request->id);
        $detalleServicios = $repo->findByListDetalleServicios($request->id);

        foreach ($participantes as $key) {
            $detalle = array();
            foreach ($detalleServicios as $value) {
                $presupuesto = $repo->findByPresupuestoDetalle($value->id_detalle, $key->id_solicitud);
                $detalle[] = array(
                    'id_servicio' => $value->id_servicio,
                    'cantidad' => $value->cantidad,
                    'observaciones' => $value->observaciones,
                    'id_internacion_domiciliaria' => $value->id_internacion_domiciliaria,
                    'presupuesto' => $presupuesto,
                    'servicio' => $value->servicio,
                    'id_detalle' => $value->id_detalle,
                    'importe_total'=> $presupuesto?->importe_total
                );
                $presupuesto = null;
            }
            $key->setAttribute('detalle', $detalle);
        }

        return response()->json($participantes);
    }

    public function getCargarPresupuestos(Request $request, InternacionDomiciliariaPresupuestosRepository $repo, InternacionDomiciliariaRepository $repoDom)
    {
        try {
            DB::beginTransaction();
            $repo->findByCargarPresupuestoSolicitado(json_decode($request->detalle));
            $repoDom->findByUpdateEstadoId($request->id, 5);
            DB::commit();
            return response()->json(['message' => 'Presupuesto cargados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getCargarAdjunto(InternacionDomiciliariaPresupuestosRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $archivo = $storageFile->findBycargarArchivo("INT_DOMIC_" . $request->id_solicitud, '/internaciones/domiciliaria', $request);
        $repo->findByAgregarAdjuntoSolicitud($request->id_solicitud, $archivo);
        return response()->json(['message' => 'Archivo cargado correctamente']);
    }

    public function getVerAdjunto(InternacionDomiciliariaPresupuestosRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "/internaciones/domiciliaria/";
        $data = $repo->findBySolicitudId($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarPropuesta(InternacionDomiciliariaPresupuestosRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "/internaciones/domiciliaria/";
        $data = $repo->findBySolicitudId($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        $storageFile->findByDeleteFileName($path);
        $repo->findByAgregarAdjuntoSolicitud($request->id, null);
        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }

    public function getAsignarGanador(InternacionDomiciliariaPresupuestosRepository $repo, Request $request, InternacionDomiciliariaRepository $repoDom)
    {
        try {
            DB::beginTransaction();
            $message = "";
            if ($request->isHistorial === 'NO') {
                $repo->findByAsignarGanadorPresupuesto(json_decode($request->detalle));
                $repoDom->findByUpdateEstadoId($request->id, 2);
                $message = "Ganador asignado correctamente";
            } else {
                $repo->findByHistorialCosto(json_decode($request->detalle),$request->id);
                $message = "Presupuesto actualizado correctamente.";
            }

            DB::commit();
            return response()->json(['message' => $message]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
