<?php

namespace App\Http\Controllers\medicacion_alto_costo;

use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;
use App\Models\medicacionAltoCosto\MedicacionAltoCosto;
use App\Models\medicacionAltoCosto\MedicacionAltoCostoDetalle;
use App\Models\medicacionAltoCosto\MedicacionAltoCostoPresupuesto;
use App\Models\medicacionAltoCosto\MedicacionAltoCostoPresupuestoDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicacionAltoCostoPresupuestosController extends Controller
{
    public function postsaveParticipantesLicitacion(Request $request)
    {

        $user = Auth::user();
        $now = Carbon::now('America/Argentina/Buenos_Aires');


        // Validación de la solicitud
        $request->validate([
            'id_medicacion_alto_costo' => 'required|integer|exists:tb_medicacion_alto_costo,id_medicacion_alto_costo',
            'detalle' => 'required|array',
            'detalle.*.cod_prestador' => 'required|integer',
        ]);

        // Iniciamos la transacción para evitar inconsistencias
        DB::beginTransaction();

        try {

            $existsPrestadoresDB = MedicacionAltoCosto::findOrFail($request->id_medicacion_alto_costo);

            // Verificar si existen presupuestos antes de intentar eliminarlos
            $presupuestos = $existsPrestadoresDB->presupuesto()->get();
            if ($presupuestos->isNotEmpty()) {
                // Si existen, eliminar los detalles y luego los presupuestos
                foreach ($presupuestos as $presupuesto) {
                    $presupuesto->detalle()->delete();  // Eliminar los detalles del presupuesto
                    $presupuesto->delete();  // Eliminar el presupuesto
                }
            }

            // Obtener detalles de cotización
            $detalleCotizacion = MedicacionAltoCostoDetalle::with('producto')
                ->where('estado_registro', 'ACTIVO')
                ->where('id_medicacion_alto_costo', $existsPrestadoresDB->id_medicacion_alto_costo)
                ->get();

            foreach ($request->detalle as $item) {
                // if ($item->id_presupuesto != '') {
                // Crear presupuesto para cada prestador
                $presupuesto = MedicacionAltoCostoPresupuesto::create([
                    'id_medicacion_alto_costo' => $existsPrestadoresDB->id_medicacion_alto_costo,
                    'cod_prestador' => $item['cod_prestador'],
                    'fecha_solicitud_presupuesto' => $now,
                    'cod_usuario' => $user->cod_usuario,
                ]);

                // Crear presupuesto detalle
                foreach ($detalleCotizacion as $item) {
                    // return response()->json(['message' => 'Solicitud de presupuesto registrada correctamente' . $item['cantidad']], 500);
                    MedicacionAltoCostoPresupuestoDetalle::create([
                        'id_presupuesto' => $presupuesto->id_presupuesto,
                        'id_vademecum' => $item['id_vademecum'],
                        'cantidad' => $item['cantidad'],
                        'precio_unitario' => 0.00,
                        'precio_total' => 0.00,
                    ]);
                }
                // }
            }

            //Actualizo estado de licitacion a Pendiente de presupuesto
            $existsPrestadoresDB->id_tipo_autorizacion = 5;
            $existsPrestadoresDB->update();

            // Commit de la transacción
            DB::commit();

            return response()->json(['message' => 'Participantes de licitación registrados correctamente'], 200);
        } catch (\Exception $e) {
            // Si algo falla, deshacer la transacción
            DB::rollBack();
            return response()->json(['error' => 'Hubo un error al registrar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function getObtenerListaParticipantes($id)
    {
        $result = MedicacionAltoCostoPresupuesto::with('prestador')->where('id_medicacion_alto_costo', $id)->get();

        return response()->json($result, 200);
    }

    public function getObtenerListaParticipantesProductos($id)
    {

        $detalleCotizacion = MedicacionAltoCostoPresupuesto::with('detalle', 'detalle.producto', 'prestador')
            ->where('id_medicacion_alto_costo', $id)->get();

        return response()->json($detalleCotizacion, 200);
    }

    public function postCargarArchivoLicitacion(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        $archivo = $storageFile->findBycargarArchivo("COTIZACION_" . $request->id_presupuesto, 'medicacionAltoCosto/cotizaciones', $request);

        $propuesta = MedicacionAltoCostoPresupuesto::findOrFail($request->id_presupuesto);
        $propuesta->archivo_cotizacion = $archivo;
        $propuesta->update();
        return response()->json(['message' => 'Archivo cargado correctamente']);
    }

    public function getAdjuntoCotizacion(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        $path = "medicacionAltoCosto/cotizaciones/";
        $data = MedicacionAltoCostoPresupuesto::findOrFail($request->id_presupuesto);
        $anioTrabajo = Carbon::parse($data->fecha_solicitud_presupuesto)->year;
        $path .= "{$anioTrabajo}/$data->archivo_cotizacion";

        return $storageFile->findByObtenerArchivo($path);
    }

    public function deleteAdjuntoCotizacion(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        $path = "medicacionAltoCosto/cotizaciones/";
        $data = MedicacionAltoCostoPresupuesto::findOrFail($request->id_presupuesto);
        $anioTrabajo = Carbon::parse($data->fecha_solicitud_presupuesto)->year;
        $path .= "{$anioTrabajo}/$data->archivo_cotizacion";

        $storageFile->findByDeleteFileName($path);
        $propuesta = MedicacionAltoCostoPresupuesto::findOrFail($request->id_presupuesto);
        $propuesta->archivo_cotizacion = '';
        $propuesta->update();
        return response()->json(['message' => 'Archivo eliminado correctamente']);
    }

    public function postSaveDetallePresupuesto(Request $request)
    {
        DB::beginTransaction();
        try {
            $detalle = json_decode($request->detalle);
            foreach ($detalle as $row) {
                foreach ($row->productos as $item) {
                    if (!is_null($item->id_detalle)) {
                        $detail = MedicacionAltoCostoPresupuestoDetalle::findOrFail($item->id_detalle);
                        $detail->precio_unitario = $item->precio_unitario;
                        $detail->precio_total = $item->precio_total;
                        $detail->observaciones = $item->observaciones;
                        $detail->update();
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'Se cargo presupuestos con exito'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function postSaveGanadorLicitacion(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now('America/Argentina/Buenos_Aires');
        DB::beginTransaction();
        try {
            $detalle = json_decode($request->detalle);
            foreach ($detalle as $item) {
                $participante = MedicacionAltoCostoPresupuesto::findOrFail($item->id_presupuesto);
                $participante->gano_licitacion = $item->gano_licitacion;
                $participante->fecha_registro_ganador = $now;
                $participante->cod_usuario_registra_ganador = $user->cod_usuario;
                $participante->update();
            }

            //Cambiar estado de medicacion alto costo a Autorizado
            $medicacion = MedicacionAltoCosto::findOrFail($request->id_medicacion_alto_costo);
            $medicacion->id_tipo_autorizacion = 2;
            $medicacion->update();

            DB::commit();
            return response()->json(['message' => 'Ganador Asignado correctamente'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'code' => $th->getCode(),
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
