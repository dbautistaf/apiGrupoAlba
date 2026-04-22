<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Models\Tesoreria\TesCashEgreso;
use App\Models\Prestadores\PrestadorImputacionesContablesEntity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class TesCashEgresosController extends Controller
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function postCrear(Request $request)
    {
        $params = (object) $request->all();

        return TesCashEgreso::create([
            'id_tipo_imputacion' => $params->id_tipo_imputacion,
            'id_prestador' => $params->id_prestador,
            'detalle' => $params->detalle,
            'fecha_emision' => $params->fecha_emision ?? $this->fechaActual,
            'fecha_cobro' => $params->fecha_cobro,
            'numero_comprobante' => $params->numero_comprobante,
            'pendiente_abonar' => $params->pendiente_abonar,
            'abonado' => $params->abonado,
            'usuario_crea' => $this->user->cod_usuario,
        ]);
    }

    public function findByUpdate($params, $id)
    {
        $egreso = TesCashEgreso::findOrFail($id);

        $egreso->id_tipo_imputacion = $params->id_tipo_imputacion;
        $egreso->id_prestador = $params->id_prestador;
        $egreso->detalle = $params->detalle;
        $egreso->fecha_emision = $params->fecha_emision;
        $egreso->fecha_cobro = $params->fecha_cobro;
        $egreso->numero_comprobante = $params->numero_comprobante;
        $egreso->pendiente_abonar = $params->pendiente_abonar;
        $egreso->abonado = $params->abonado;
        $egreso->update();

        return $egreso;
    }

    public function getListEgresos(Request $request)
    {
        // Parseo de fechas
        $desde = Carbon::parse($request->desde)->startOfDay();
        $hasta = Carbon::parse($request->hasta)->endOfDay();

        // Parámetros de paginación
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Consulta original intacta
        $query = TesCashEgreso::with(['prestador', 'tipoImputacion', 'usuario'])
            ->when(
                $request->filled('desde'),
                fn($q) =>
                $q->whereDate('fecha_emision', '>=', $desde)
            )
            ->when(
                $request->filled('hasta'),
                fn($q) =>
                $q->whereDate('fecha_emision', '<=', $hasta)
            );

        // Agregamos paginación a tu resultado
        $result = $query->orderByDesc('id_cash_egresos')
            ->paginate($perPage, ['*'], 'page', $page);

        // Transformación para front
        $data = $result->getCollection()->transform(function ($item) {
            return [
                'id_cash_egresos' => $item->id_cash_egresos,
                'detalle' => $item->detalle,
                'fecha_emision' => $item->fecha_emision,
                'fecha_cobro' => $item->fecha_cobro,
                'numero_comprobante' => $item->numero_comprobante,
                'pendiente_abonar' => $item->pendiente_abonar,
                'abonado' => $item->abonado,
                'usuario' => $item->usuario->usuario ?? '',
                'prestador' => [
                    'cuit' => $item->prestador->cuit ?? '',
                    'razon_social' => $item->prestador->razon_social ?? ''
                ],
                'tipoImputacion' => [
                    'id_tipo_imputacion_contable' => $item->tipoImputacion->id_tipo_imputacion_contable ?? null,
                    'descripcion' => $item->tipoImputacion->descripcion ?? ''
                ]
            ];
        });

        // Devolvemos la respuesta con formato estándar
        return response()->json([
            'data' => $data,
            'total' => $result->total(),
            'current_page' => $result->currentPage(),
            'per_page' => $result->perPage(),
        ]);
    }



    public function getEgresoById($id)
    {
        $egreso = TesCashEgreso::with(['prestador', 'tipoImputacion', 'usuario'])->find($id);

        if (!$egreso) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }

        $data = [
            // Estas claves coinciden con el formGroup
            'cuitPrestador' => $egreso->prestador->cod_prestador ?? null,
            'detalle' => $egreso->detalle,
            'imputacionCash' => $egreso->tipoImputacion->id_tipo_imputacion_contable ?? null,
            'fechaEmisionPago' => $egreso->fecha_emision,
            'fechaCobro' => $egreso->fecha_cobro,
            'numeroComprobante' => $egreso->numero_comprobante,
            'pendienteAbonar' => $egreso->pendiente_abonar,
            'abonado' => $egreso->abonado,
        ];

        return response()->json($data, 200);
    }


    public function getImputacionesByPrestador($idPrestador)
    {
        return PrestadorImputacionesContablesEntity::with('imputacion')
            ->where('cod_prestador', $idPrestador)
            ->where('vigente', true)
            ->get()
            ->pluck('imputacion');
    }

    public function eliminarEgreso($id)
    {
        $egreso = TesCashEgreso::find($id);

        if (!$egreso) {
            return response()->json(['message' => 'Egreso no encontrado'], 404);
        }

        try {
            $egreso->delete();
            return response()->json(['message' => 'Egreso eliminado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el egreso', 'error' => $e->getMessage()], 500);
        }
    }
}
