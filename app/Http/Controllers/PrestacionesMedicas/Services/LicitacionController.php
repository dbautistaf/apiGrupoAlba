<?php

namespace App\Http\Controllers\PrestacionesMedicas\Services;

use App\Http\Controllers\PrestacionesMedicas\Repository\LicitacionLentesRepository;
use App\Http\Controllers\PrestacionesMedicas\Repository\SolicitudLentesFilterRepository;
use App\Http\Controllers\PrestacionesMedicas\Repository\SolicitudLentesRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class LicitacionController extends Controller
{

    public function getProcesarDetalle(LicitacionLentesRepository $repo, SolicitudLentesRepository $repoSoli, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findBySavePrestadores($request->detalle, $request->id_solitud_lente);
            $repoSoli->findByUpdateEstado($request->id_solitud_lente, 2);
            DB::commit();
            return response()->json(['message' => 'Detalle de prestadores guardados correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getDetalleParticipantes(LicitacionLentesRepository $repo,  Request $request)
    {
        return response()->json($repo->findByListParticipantes(($request->id)));
    }

    public function getDetalleParticipantesPresupuestos(Request $request, LicitacionLentesRepository $repo)
    {
        $participantes = $repo->findByListParticipantes($request->id);
        return response()->json(["participantes" => $participantes]);
    }

    public function getAdjuntarPropuesta(LicitacionLentesRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $archivo = $storageFile->findBycargarArchivo("COTIZACION_" . $request->id, 'lentes/cotizaciones', $request);

        $repo->findByCargarArchivoPropuesta($archivo, $request->id);
        return response()->json(['message' => 'Archivo adjuntado correctamente']);
    }

    public function getVerAdjunto(LicitacionLentesRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "lentes/cotizaciones/";
        $data = $repo->findByPropuestaId($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarAdjunto(LicitacionLentesRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "lentes/cotizaciones/";
        $data = $repo->findByPropuestaId($request->id);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        $storageFile->findByDeleteFileName($path);
        $repo->findByCargarArchivoPropuesta(null, $request->id);
        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }

    public function getProcesarDetallePresupuesto(LicitacionLentesRepository $repo, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findBySaveDetallePresupuesto(json_decode($request->detalle));
            DB::commit();
            return response()->json(['message' => 'Detalle guardado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getAsignarGanador(LicitacionLentesRepository $repo, SolicitudLentesRepository $repoSoli, Request $request)
    {
        DB::beginTransaction();
        try {
            $existeGanador = $repo->findByAsignarGanador(json_decode($request->detalle));
            if (!$existeGanador) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Seleccione un ganador para poder continuar con el proceso'
                ], 409);
            }
            $repoSoli->findByUpdateEstado($request->id, 3);
            DB::commit();
            return response()->json(['message' => 'Ganador Asignado correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
