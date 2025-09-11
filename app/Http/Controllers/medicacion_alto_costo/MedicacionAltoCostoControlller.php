<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Http\Controllers\medicacion_alto_costo\Repository\MedicacionAltoCostoRepository;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\medicacionAltoCosto\MedicacionAltoCosto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class MedicacionAltoCostoControlller extends Controller
{
    public function getLikeMedicacionAltoCosto(Request $request)
    {

        try {
            $datos = $request;
            $query = MedicacionAltoCosto::with([
                'afiliado',
                'estado',
                'autorizacion',
                'detalle' => function ($q) {
                    $q->where('estado_registro', 'ACTIVO');
                },
                'detalle.producto',
                'detalle.cobertura',
                'comprobantes'
            ]);
            $query->where('estado_registro', 'ACTIVO');

            if ($datos['numero_tramite'] != '') {
                $query->where('numero_tramite', $request->numero_tramite);
            }

            if (isset($datos['search']) && $datos['search'] != '') {
                $query->where(function ($query) use ($datos) {
                    $query->whereHas('afiliado', function ($queryAfiliado) use ($datos) {
                        $queryAfiliado->where('dni', 'LIKE', "{$datos['search']}%")
                            ->orWhere('nombre', 'LIKE', "{$datos['search']}%")
                            ->orWhere('apellidos', 'LIKE', "{$datos['search']}%");
                    });
                });
            }

            if (isset($datos['id_tipo_autorizacion']) && $datos['id_tipo_autorizacion'] != '') {
                $query->where('id_tipo_autorizacion', $request->id_tipo_autorizacion);
            }

            if (isset($datos['desde']) && $datos['desde'] != '' && isset($datos['hasta']) && $datos['hasta'] != '') {
                $query->whereBetween('fecha_registro', [$request->desde, $request->hasta]);
            }
            $query->orderByDesc('id_medicacion_alto_costo');
            $result = $query->get();

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ha ocurrido un error en el servidor.', 'message' => $e->getMessage()], 500);
        }
    }

    public function postSaveMedicacionAltoCosto(Request $request, ManejadorDeArchivosUtils $storageFile, MedicacionAltoCostoRepository $repo)
    {
        DB::BeginTransaction();
        try {
            $datos = json_decode($request->json);
            $path = "medicacion_alto_costo/cotizaciones/";

            if (is_numeric($datos->id_medicacion_alto_costo)) {
                $medicacion = $repo->findByUpdate($datos);

                if (count($request->archivos) > 0) {
                    $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos("MEDICACION_" . $datos->id_tipo_autorizacion, $path, $request);
                    $repo->findByAgregarDetalleArchivos($archivosAdjuntos, $medicacion);
                }

                $repo->findByCrearDetalleMedicamentos($datos->detalle, $medicacion);
                DB::commit();
                return response()->json(["message" => "Registro modificado exitosamente"], 200);
            } else {
                $medicacion = $repo->findByCrear($datos);

                if (count($request->archivos) > 0) {
                    $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos("MEDICACION_" . $datos->id_tipo_autorizacion, $path, $request);
                    $repo->findByAgregarDetalleArchivos($archivosAdjuntos, $medicacion);
                }

                $repo->findByCrearDetalleMedicamentos($datos->detalle, $medicacion);

                DB::commit();
                return response()->json(["message" => "Registro procesado exitosamente"], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function getDataEditMedicacionAltoCosto(Request $request)
    {
        $medicacion = MedicacionAltoCosto::with(['detalle' => function ($q) {
            $q->where('estado_registro', 'ACTIVO');
        }, 'afiliado', 'afiliado.detalleplan'])
            ->where('id_medicacion_alto_costo', $request->id)
            ->first();
        return response()->json($medicacion);
    }

    public function getMedicacionAltoCostoById(Request $request)
    {
        $medicacion = MedicacionAltoCosto::with(['detalle' => function ($q) {
            $q->where('estado_registro', 'ACTIVO');
        }, 'afiliado', 'autorizacion'])
            ->where('id_medicacion_alto_costo', $request->id)
            ->first();

        return response()->json($medicacion);
    }

    public function deleteMedicacionAltoCosto(Request $request, MedicacionAltoCostoRepository $repo)
    {
        try {
            $repo->findByEliminarMedicacionAltoCosto($request);
            return response()->json(['message' => 'Registro eliminado con exito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un error al eliminar registro ' . $e->getMessage()], 500);
        }
    }

    public function deleteItemMedicacionAltoCosto(Request $request, MedicacionAltoCostoRepository $repo)
    {
        try {
            $repo->findByEliminarItenDetalle($request);
            return response()->json(['message' => 'Registro eliminado con exito'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Hubo un error al eliminar registro ' . $e->getMessage()], 500);
        }
    }

    public function getVerAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request, MedicacionAltoCostoRepository $repo)
    {
        $path = "medicacion_alto_costo/cotizaciones/";
        $data = $repo->findByIdComprobante($request->id_comprobante);
        $anioTrabaja = Carbon::parse($data->fecha_registra)->year;
        $path .= "{$anioTrabaja}/$data->nombre_archivo";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function getAfiliadoMedicamentos(Request $request)
    {
        try {
            $query = MedicacionAltoCosto::with(['afiliado', 'estado', 'autorizacion', 'detalle' => function ($q) {
                $q->where('estado_registro', 'ACTIVO');
            }, 'detalle.producto', 'detalle.cobertura'])
                ->where('dni_afiliado', $request->dni);
            $query->orderByDesc('id_medicacion_alto_costo');
            $result = $query->get();

            return response()->json($result, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ha ocurrido un error en el servidor.', 'message' => $e->getMessage()], 500);
        }
    }
}
