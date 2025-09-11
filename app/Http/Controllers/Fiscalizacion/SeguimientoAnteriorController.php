<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\SeguimientoAnterior;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class SeguimientoAnteriorController extends Controller
{
    /**
     * Obtener todos los registros con filtros opcionales y paginación.
     */
    public function getListSeguimientos(Request $request)
    {
        // Definir los parámetros de paginación
        $perPage = $request->input('per_page', 10); // Por defecto 10 resultados por página
        $page = $request->input('page', 1); // Página actual

        // Construir la consulta base
        $query = SeguimientoAnterior::query();

        // Aplicar filtros según los parámetros recibidos
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
            $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
            $query->whereBetween('fecha', [$fechaDesde, $fechaHasta]);
        } elseif ($request->filled('fecha_desde')) {
            $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
            $query->where('fecha', '>=', $fechaDesde);
        } elseif ($request->filled('fecha_hasta')) {
            $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();
            $query->where('fecha', '<=', $fechaHasta);
        }


        if ($request->has('cuit')) {
            $query->where('cuit', 'like', '%' . $request->cuit . '%');
        }

        if ($request->has('razon_social')) {
            $query->where('razon_social', 'like', '%' . $request->razon_social . '%');
        }

        if ($request->has('deuda_ospf')) {
            $query->where('deuda_ospf', '=', $request->deuda_ospf);
        }

        if ($request->has('usuario')) {
            $query->where('usuario', 'like', '%' . $request->usuario . '%');
        }

        // Obtener el total de registros para la paginación
        $total = $query->count();

        $query->orderBy('fecha', 'desc');

        // Obtener los resultados con paginación
        $result = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Devolver la respuesta con los datos paginados
        return response()->json([
            'data' => $result,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage
        ], 200);
    }

    //Cantidad de intimaciones activas por cuit
    public function contarIntimacionesActivas(Request $request)
    {
        $request->validate([
            'cuit' => 'required|string'
        ]);

        $cantidad = SeguimientoAnterior::where('cuit', $request->cuit)
            ->where('finalizado', 'NO')
            ->count();

        return response()->json([
            'cuit' => $request->cuit,
            'cantidad' => $cantidad
        ]);
    }
}
