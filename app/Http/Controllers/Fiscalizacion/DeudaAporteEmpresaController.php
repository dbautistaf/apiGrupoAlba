<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\DeudaAporteEmpresa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DeudaAporteEmpresaController extends Controller
{
    public function index()
    {
        return DeudaAporteEmpresa::all();
    }

    public function buscarPorEmpresa($idempresa)
    {
        return DeudaAporteEmpresa::where('id_empresa', $idempresa)
            ->where('estado', 'Vigente')
            ->get();
    }

    public function getListDeudas(Request $request)
    {
        // Construir la consulta base con la relación empresa
        $query = DeudaAporteEmpresa::with('empresa')
            ->where('estado', 'Vigente');

        // Filtros por fechas de recálculo
        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereDate('fecha_recalculo', '>=', $request->desde)
                ->whereDate('fecha_recalculo', '<=', $request->hasta);
        } elseif ($request->filled('desde')) {
            $query->whereDate('fecha_recalculo', '>=', $request->desde);
        } elseif ($request->filled('hasta')) {
            $query->whereDate('fecha_recalculo', '<=', $request->hasta);
        }

        // Filtro por año
        if ($request->filled('anio')) {
            $query->where('anio', '=', $request->anio);
        }

        // Filtro por mes
        if ($request->filled('mes')) {
            $query->where('mes', '=', $request->mes);
        }

        // Filtro por monto de deuda (desde/hasta)
        if ($request->filled('monto_desde') && $request->filled('monto_hasta')) {
            $query->whereBetween('monto_deuda', [$request->monto_desde, $request->monto_hasta]);
        } elseif ($request->filled('monto_desde')) {
            $query->where('monto_deuda', '>=', $request->monto_desde);
        } elseif ($request->filled('monto_hasta')) {
            $query->where('monto_deuda', '<=', $request->monto_hasta);
        }

        // Filtro por nombre de empresa (requiere join)
        if ($request->filled('nombre_empresa')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('razon_social', 'like', '%' . $request->nombre_empresa . '%');
            });
        }

        // Filtro por CUIT (requiere join)
        if ($request->filled('cuit')) {
            $query->whereHas('empresa', function ($q) use ($request) {
                $q->where('cuit', 'like', '%' . $request->cuit . '%');
            });
        }

        // Paginación
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $total = $query->count();

        $query->orderBy('fecha_recalculo', 'desc');

        $result = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Devolver la respuesta en formato JSON
        return response()->json([
            'data' => $result,
            'total' => $total
        ], 200);
    }

    // Detalle de deuda con intereses
    public function detalleDeuda(Request $request)
    {
        $periodo = $request->input('periodo'); // YYMM
        $cuit = $request->input('cuit');

        if (!$periodo || !$cuit) {
            return response()->json(['error' => 'Faltan parámetros: periodo y cuit'], 400);
        }

        // Traer todas las deudas del periodo
        $deudas = \DB::table('tb_declaraciones_juradas as ddjj')
            ->selectRaw("
            ddjj.periodo,
            ddjj.cuil,
            ddjj.cuit,
            ddjj.remimpo AS importe_sueldo,
            ROUND(ddjj.remimpo * 0.03, 2) AS aporte,
            ROUND(ddjj.remimpo * 0.06, 2) AS contribucion,
            ddjj.fecpresent AS fecha_recalculo,
            DATE_ADD(ddjj.fecpresent, INTERVAL 30 DAY) AS fecha_vencimiento,
            emp.id_empresa,
            emp.razon_social,
            ROUND(ddjj.remimpo * 0.09, 2) AS monto_deuda,
            'APORTE' AS tipo_deuda,
            'Vigente' AS estado
        ")
            ->leftJoin('tb_transferencias as trf', function ($join) {
                $join->on('ddjj.cuil', '=', 'trf.cuitcont')
                    ->on('ddjj.periodo', '=', 'trf.periodo');
            })
            ->join('tb_empresa as emp', 'emp.cuit', '=', 'ddjj.cuit')
            ->whereNull('trf.id_transferencia')
            ->where('ddjj.periodo', $periodo)
            ->where('emp.cuit', $cuit)
            ->orderBy('ddjj.fecpresent')
            ->get();

        if ($deudas->isEmpty()) {
            return response()->json(['error' => 'No se encontraron deudas para el periodo y CUIT especificados'], 404);
        }

        $hoy = now()->toDateString();

        // Calcular intereses y total de cada deuda
        $deudas->transform(function ($deuda) use ($hoy) {

            $tasas = \DB::table('tb_fisca_tasas_interes')
                ->where('articulo_resolucion', 'Artículo 1°')
                ->where(function ($q) use ($deuda, $hoy) {
                    $q->where('vigencia_inicio', '<=', $hoy)
                        ->where(function ($q2) use ($deuda) {
                            $q2->whereNull('vigencia_fin')
                                ->orWhere('vigencia_fin', '>=', $deuda->fecha_vencimiento);
                        });
                })
                ->orderBy('vigencia_inicio')
                ->get();

            $interesCalculado = 0;

            foreach ($tasas as $tasa) {
                $inicio = \Carbon\Carbon::parse($deuda->fecha_vencimiento)->greaterThan(\Carbon\Carbon::parse($tasa->vigencia_inicio))
                    ? \Carbon\Carbon::parse($deuda->fecha_vencimiento)
                    : \Carbon\Carbon::parse($tasa->vigencia_inicio);

                $fin = \Carbon\Carbon::parse($tasa->vigencia_fin ?? $hoy)->lessThan(\Carbon\Carbon::parse($hoy))
                    ? \Carbon\Carbon::parse($tasa->vigencia_fin)
                    : \Carbon\Carbon::parse($hoy);

                $dias = $inicio->diffInDays($fin) + 1;

                $interesCalculado += $deuda->monto_deuda * $tasa->interes_diario * $dias;
            }

            $deuda->intereses = round($interesCalculado, 2);
            $deuda->monto_total = round($deuda->monto_deuda + $interesCalculado, 2);

            return $deuda;
        });

        return response()->json($deudas, 200);
    }

    //Detalle de deuda - funcionando pero no contempla intereses
    // public function detalleDeuda(Request $request)
    // {
    //     $periodo = $request->input('periodo'); // formato YYMM
    //     $cuit = $request->input('cuit');

    //     if (!$periodo || !$cuit) {
    //         return response()->json(['error' => 'Faltan parámetros: periodo y cuit'], 400);
    //     }

    //     $deuda = \DB::table('tb_declaraciones_juradas as ddjj')
    //         ->selectRaw("
    //             ddjj.periodo,
    //             ddjj.cuil,
    //             ddjj.cuit,
    //             ddjj.remimpo AS importe_sueldo,
    //             ROUND(ddjj.remimpo * 0.03, 2) AS aporte,
    //             ROUND(ddjj.remimpo * 0.06, 2) AS contribucion,
    //             ddjj.fecpresent AS fecha_recalculo,
    //             DATE_ADD(ddjj.fecpresent, INTERVAL 30 DAY) AS fecha_vencimiento,
    //             emp.id_empresa,
    //             emp.razon_social,
    //             ROUND(ddjj.remimpo * 0.09, 2) AS monto_deuda,
    //             'APORTE' AS tipo_deuda,
    //             'Vigente' AS estado
    //         ")
    //         ->leftJoin('tb_transferencias as trf', function ($join) {
    //             $join->on('ddjj.cuil', '=', 'trf.cuitcont')
    //                 ->on('ddjj.periodo', '=', 'trf.periodo');
    //         })
    //         ->join('tb_empresa as emp', 'emp.cuit', '=', 'ddjj.cuit')
    //         ->whereNull('trf.id_transferencia')
    //         ->where('ddjj.periodo', $periodo)
    //         ->where('emp.cuit', $cuit)
    //         ->orderBy('ddjj.fecpresent')
    //         ->get();

    //     if ($deuda->isEmpty()) {
    //         return response()->json(['error' => 'No se encontró deuda para el periodo y CUIT especificados'], 404);
    //     }

    //     return response()->json($deuda, 200);
    // }
}
