<?php

namespace   App\Http\Controllers\Protesis\Services;

use App\Http\Controllers\Protesis\Repository\DetalleCotizacionRepository;
use App\Http\Controllers\Protesis\Repository\DetallePrestadoresLicitacionRepository;
use App\Http\Controllers\Protesis\Repository\ProtesisRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class DetallePrestadoreslicitacionController extends Controller
{

    public function getProcesarDetalle(DetallePrestadoresLicitacionRepository $repo, ProtesisRepository $repoProtesis, Request $request)
    {
        $repo->findBySavePrestadores($request->detalle, $request->id_protesis);
        $repoProtesis->findByUpdateEstado($request->id_protesis, 1);

        return response()->json(['message' => 'Detalle de prestadores guardados correctamente']);
    }

    public function getObtenerListaParticipantes(DetallePrestadoresLicitacionRepository $repo, Request $request)
    {
        return response()->json($repo->findByListParticipantesConvocatoria($request->id));
    }

    public function getListarMatrizParticipantesProductos(DetallePrestadoresLicitacionRepository $repo, DetalleCotizacionRepository $repoCotizacion, Request $request)
    {
        $participantes = $repo->findByListParticipantesConvocatoria($request->id);

        $detalleCotizacion = $repoCotizacion->findByDetalleCotizacion($request->id);
        foreach ($participantes as $key) {
            $detalleProducto = [];
            foreach ($detalleCotizacion as $value) {
                if ($key->id_solicitud === $value->id_solicitud) {
                    $detalleProducto[] = $value;
                    break;
                }
            }
            $key->detalle = $detalleProducto;
        }

        return response()->json(["participantes" => $participantes]);
    }

    public function getCargarPropuesta(DetallePrestadoresLicitacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $archivo = $storageFile->findBycargarArchivo("COTIZACION_" . $request->id_solicitud, 'protesis/cotizaciones', $request);

        $repo->findByCargarPropuesta($archivo, $request->id_solicitud);
        return response()->json(['message' => 'Archivo cargado correctamente']);
    }

    public function getVerAdjunto(DetallePrestadoresLicitacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "protesis/cotizaciones/";
        $data = $repo->findByDetalleId($request->id_solicitud);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getEliminarPropuesta(DetallePrestadoresLicitacionRepository $repo, ManejadorDeArchivosUtils $storageFile, Request $request)
    {
        $path = "protesis/cotizaciones/";
        $data = $repo->findByDetalleId($request->id_solicitud);
        $anioTrabaja = Carbon::parse($data->fecha_solicita_presupuesto)->year;
        $path .= "{$anioTrabaja}/$data->archivo_cotizacion";

        $storageFile->findByDeleteFileName($path);
        $repo->findByCargarPropuesta(null, $request->id_solicitud);
        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }

    public function getCargarDetallePropuesta(DetalleCotizacionRepository $repo,   Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findBySaveDetalleCotizacion(json_decode($request->detalle), $request->id_protesis);
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

    public function getAsignarGanadorLicitacion(DetallePrestadoresLicitacionRepository $repo, ProtesisRepository $repoProtesis,  Request $request)
    {
        DB::beginTransaction();
        try {
            $repo->findByAsignarGanadorLicitacion(json_decode($request->detalle));
            $repoProtesis->findByUpdateEstado($request->id_protesis, 2);
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
