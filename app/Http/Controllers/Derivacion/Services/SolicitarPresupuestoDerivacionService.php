<?php

namespace App\Http\Controllers\Derivacion\Services;

use App\Http\Controllers\Derivacion\Repository\DerivacionRepository;
use App\Http\Controllers\Derivacion\Repository\SolicitarPresupuestoDerivacionRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class SolicitarPresupuestoDerivacionService extends Controller
{

    public function getProcesarDetalle(SolicitarPresupuestoDerivacionRepository $repo, DerivacionRepository $repoDerivacion, Request $request)
    {
        $repo->findBySavePrestadores($request->detalle, $request->id_derivacion);
        $repoDerivacion->findByUpdateEstadoPresupuesto($request->id_derivacion, 1);

        return response()->json(['message' => 'Detalle de prestadores guardados correctamente']);
    }

    public function getObtenerListaParticipantes(SolicitarPresupuestoDerivacionRepository $repo, Request $request)
    {
        return response()->json($repo->findByListParticipantesConvocatoria($request->id));
    }

    public function getListarMatrizParticipantesProductos(SolicitarPresupuestoDerivacionRepository $repo, Request $request)
    {
        $participantes = $repo->findByListParticipantesConvocatoria($request->id);
        return response()->json(["participantes" => $participantes]);
    }

    public function getCargarPropuesta(SolicitarPresupuestoDerivacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $archivo = $storageFile->findBycargarArchivo("COTIZACION_" . $request->id_solicitud, 'derivaciones/cotizaciones', $request);

        $repo->findByCargarPropuesta($archivo, $request->id_solicitud);
        return response()->json(['message' => 'Archivo cargado correctamente']);
    }

    public function getVerAdjunto(SolicitarPresupuestoDerivacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "derivaciones/cotizaciones/";
        $data = $repo->findByDetalleId($request->id_solicitud);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarPropuesta(SolicitarPresupuestoDerivacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "derivaciones/cotizaciones/";
        $data = $repo->findByDetalleId($request->id_solicitud);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        $storageFile->findByDeleteFileName($path);
        $repo->findByCargarPropuesta(null, $request->id_solicitud);
        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }

    public function getCargarDetallePropuesta(SolicitarPresupuestoDerivacionRepository $repo, DerivacionRepository $repoDerivacion, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findBySaveDetalleCotizacion(json_decode($request->detalle));
            $repoDerivacion->findByUpdateEstadoPresupuesto($request->id_derivacion, 2);
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

    public function getAsignarGanadorLicitacion(SolicitarPresupuestoDerivacionRepository $repo, DerivacionRepository $repoDerivacion, Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findByAsignarGanadorLicitacion(json_decode($request->detalle));
            $repoDerivacion->findByUpdateEstadoPresupuesto($request->id_derivacion, 3);
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
