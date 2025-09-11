<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\ArchivoCuotas;
use App\Models\Fiscalizacion\Cuota;
use App\Models\Fiscalizacion\DetallePeriodoExpediente;
use App\Models\Fiscalizacion\DeudaAporteEmpresa;
use App\Models\Fiscalizacion\Expediente;
use App\Models\Fiscalizacion\Intimacion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\Utils\ManejadorDeArchivosUtils;


class CuotaController extends Controller
{
    public function postSaveCuota(Request $request)
    {
        $msg = '';

        if ($request->id_cuota == '') {
            // Crear una nueva cuota
            Cuota::create([
                'id_acuerdo_pago' => $request->id_acuerdo_pago,
                'periodo' => $request->periodo,
                'importe' => $request->importe,
                'fecha_pago' => $request->fecha_pago,
                'comprobante' => $request->comprobante,
                'estado' => $request->estado,
            ]);

            $msg = 'Cuota registrada correctamente';
        } else {
            // Actualizar una cuota existente
            $cuota = Cuota::find($request->id_cuota);
            $cuota->update([
                'id_acuerdo_pago' => $request->id_acuerdo_pago,
                'periodo' => $request->periodo,
                'importe' => $request->importe,
                'fecha_pago' => $request->fecha_pago,
                'comprobante' => $request->comprobante,
                'estado' => $request->estado,
            ]);

            $msg = 'Cuota actualizada correctamente';
        }

        return response()->json(['message' => $msg], 200);
    }

    public function getListCuotas(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = Cuota::with(['acuerdoPago.empresa']);

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_vencimiento', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_vencimiento', '<=', $request->fecha_hasta);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('empresa')) {
            $query->whereHas('acuerdoPago.empresa', function ($q) use ($request) {
                $q->where('id_empresa', $request->empresa);
            });
        }

        if ($request->filled('idAcuerdoPago')) {
            $query->where('id_acuerdo_pago', $request->idAcuerdoPago);
        }

        $result = $query->orderBy('fecha_vencimiento', 'desc')->paginate($perPage, ['*'], 'page', $page);

        // Transformación
        $data = $result->getCollection()->transform(function ($item) {
            return [
                'idCuota' => $item->id_cuota,
                'nroCuota' => $item->numero_cuota,
                'idAcuerdo' => $item->acuerdoPago->id_acuerdo_pago ?? null,
                'nroActa' => $item->acuerdoPago->numero_acta,
                'razonSocial' => $item->acuerdoPago->empresa->razon_social ?? '',
                'cuit' => $item->acuerdoPago->empresa->cuit ?? '',
                'capital' => $item->capital_cuota,
                'interes' => $item->interes_cuota,
                'importe' => $item->importe_cuota,
                'fechaPago' => $item->fecha_pago,
                'formaPago' => $item->forma_pago,
                'fechaVencimiento' => $item->fecha_vencimiento,
                'estado' => $item->estado,
                'archivos' => $item->archivos->map(function ($archivo) {
                    return [
                        'idArchivo' => $archivo->id ?? null,
                        'idCuota' => $archivo->id_cuota ?? null,
                        'nombre' => $archivo->nombre_original ?? '',
                        'ruta' => $archivo->ruta ?? '',
                        'tipo' => $archivo->tipo_archivo ?? '',
                        'tamanio' => $archivo->tamaño ?? null,
                        'fechaSubida' => $archivo->fecha_subida ?? null,
                    ];
                }),
            ];
        });

        return response()->json([
            'data' => $data,
            'total' => $result->total(),
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
        ]);
    }

    public function postPagarCuota(Request $request, ManejadorDeArchivosUtils $storageFile)
    {
        \DB::beginTransaction();
        try {
            $cuota = Cuota::with('acuerdoPago')->find($request->idCuota);

            if (!$cuota) {
                return response()->json(['message' => 'Cuota no encontrada'], 404);
            }

            if ($cuota->estado === 'Pagado') {
                return response()->json(['message' => 'La cuota ya fue pagada'], 400);
            }

            // Marcar cuota como pagada
            $cuota->fecha_pago = now();
            $cuota->estado = 'Pagado';
            $cuota->forma_pago = 'Manual';
            $cuota->save();

            // Verificar si todas las cuotas del acuerdo están pagadas
            $cuotasPendientes = Cuota::where('id_acuerdo_pago', $cuota->id_acuerdo_pago)
                ->where('estado', '!=', 'Pagado')
                ->count();

            //Cancelar el acuerdo de pago si no hay cuotas pendientes
            if ($cuotasPendientes === 0) {
                $cuota->acuerdoPago->update([
                    'id_estado_acuerdo' => 4 // Cancelado
                ]);

                // También pasar el expediente a estado "Finalizado"
                if ($cuota->acuerdoPago->id_expediente) {
                    $expediente = Expediente::find($cuota->acuerdoPago->id_expediente);
                    if ($expediente) {
                        $expediente->estado = 'Finalizado';
                        $expediente->save();
                    }
                    // Marcar todas las intimaciones del expediente como finalizadas
                    Intimacion::where('id_expediente', $expediente->id_expediente)
                        ->update(['estado' => 'Finalizado']);

                    // Obtener los id_deuda de los periodos del expediente
                    $idsDeuda = DetallePeriodoExpediente::where('id_expediente', $expediente['id_expediente'])
                        ->pluck('id_deuda'); // Ej: [123, 124, 125]

                    // Finalizar solo esas deudas
                    DeudaAporteEmpresa::whereIn('id_deuda', $idsDeuda)
                        ->update(['estado' => 'Finalizado']);

                }
            }
            // \Log::info('Archivos recibidos en request:', ['archivos' => $request->archivos]);

            //Guardado de archivos adjuntos
            if (count($request->archivos) > 0) {
                $archivosAdjuntos = $storageFile->findByCargaMasivaArchivos(
                    "CUOTAS_" . $cuota->id_cuota,
                    'fiscalizacion/cuotas/archivos',
                    $request
                );
                $this->guardarArchivosAdjuntosCuota($archivosAdjuntos, $cuota->id_cuota);
            }

            \DB::commit();
            return response()->json(['message' => 'Cuota pagada correctamente'], 200);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['message' => 'Error al pagar cuota', 'error' => $e->getMessage()], 500);
        }
    }


    // public function getCuotaById($id)
    // {
    //     $query = Cuota::with('acuerdoPago')->find($id);
    //     return response()->json($query, 200);
    // }

    public function getCuotaById($id)
    {
        $cuota = Cuota::with(['acuerdoPago', 'archivos'])->find($id);

        if (!$cuota) {
            return response()->json(['message' => 'Cuota no encontrada'], 404);
        }

        $data = [
            'idCuota' => $cuota->id_cuota,
            'nroCuota' => $cuota->numero_cuota,
            'idAcuerdo' => $cuota->acuerdoPago->id_acuerdo_pago ?? null,
            'nroActa' => $cuota->acuerdoPago->numero_acta ?? null,
            'razonSocial' => $cuota->acuerdoPago->empresa->razon_social ?? '',
            'cuit' => $cuota->acuerdoPago->empresa->cuit ?? '',
            'capital' => $cuota->capital_cuota,
            'interes' => $cuota->interes_cuota,
            'importe' => $cuota->importe_cuota,
            'fechaPago' => $cuota->fecha_pago,
            'formaPago' => $cuota->forma_pago,
            'fechaVencimiento' => $cuota->fecha_vencimiento,
            'estado' => $cuota->estado,
            'archivos' => $cuota->archivos->map(function ($archivo) {
                return [
                    'idArchivo' => $archivo->id ?? null,
                    'idCuota' => $archivo->id_cuota ?? null,
                    'nombre' => $archivo->nombre_original ?? '',
                    'ruta' => $archivo->ruta ?? '',
                    'tipo' => $archivo->tipo_archivo ?? '',
                    'tamanio' => $archivo->tamaño ?? null,
                    'fechaSubida' => $archivo->fecha_subida ?? null,
                ];
            }),
        ];

        return response()->json($data, 200);
    }

    //Archivos
    public function getArchivosPorCuota($id)
    {
        $archivos = ArchivoCuotas::where('id_cuota', $id)->get();
        return response()->json($archivos, 200);
    }

    private function guardarArchivosAdjuntosCuota(array $archivosAdjuntos, int $idCuota)
    {
        foreach ($archivosAdjuntos as $archivo) {
            \Log::info('Archivo recibido:', $archivo);
            ArchivoCuotas::create([
                'id_cuota' => $idCuota,
                'nombre_original' => $archivo['nombre'] ?? 'archivo_sin_nombre',
                'ruta' => str_replace('public/', '', $archivo['ruta']),
                'tipo_archivo' => $archivo['extension'] ?? pathinfo($archivo['nombre'], PATHINFO_EXTENSION),

                'tamaño' => $archivo['tamaño'] ?? null,
                'fecha_subida' => now(),
            ]);
        }
    }

    public function getArchivoAdjunto(ManejadorDeArchivosUtils $storageFile, Request $request)
    {

        $path = "fiscalizacion/cuotas/archivos/";
        // $data = $pago->findById($request->id);
        $anioTrabaja = $request->fecha_registra;
        $path .= "{$anioTrabaja}/$request->nombre_archivo";

        \Log::info('getArchivoAdjunto cuota', [
            'fecha_registra' => $request->fecha_registra,
            'nombre_archivo' => $request->nombre_archivo,
            'path' => $path
        ]);

        return $storageFile->findByObtenerArchivo($path);
    }
}