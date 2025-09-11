<?php

namespace App\Http\Controllers\Fiscalizacion;

use App\Models\Fiscalizacion\CobranzaAnterior;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;

class CobranzaAnteriorController extends Controller
{
    /**
     * Obtener todos los registros con filtros opcionales.
     */
    public function getListCobranzas(Request $request)
    {
        // Construir la consulta base
        $query = CobranzaAnterior::query();

        // Filtros por fechas (desde/hasta) - Para campos tipo DATE
        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->whereDate('ReciboFecha', '>=', $request->desde)
                ->whereDate('ReciboFecha', '<=', $request->hasta);
        } elseif ($request->filled('desde')) {
            $query->whereDate('ReciboFecha', '>=', $request->desde);
        } elseif ($request->filled('hasta')) {
            $query->whereDate('ReciboFecha', '<=', $request->hasta);
        }

        // Otros filtros
        if ($request->filled('cuit')) {
            $query->where('cuit', 'like', '%' . $request->cuit . '%');
        }

        if ($request->filled('razon_social')) {
            $query->where('razon_social', 'like', '%' . $request->razon_social . '%');
        }

        if ($request->filled('importe')) {
            $query->where('importe', '=', $request->importe);
        }

        if ($request->filled('tipo_pago')) {
            $query->where('medio_pago', 'like', '%' . $request->tipo_pago . '%');
        }

        // Paginación
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $total = $query->count();

        $query->orderBy('fecha', 'desc');

        $result = $query->skip(($page - 1) * $perPage)->take($perPage)->get();

        // Devolver la respuesta en formato JSON
        return response()->json([
            'data' => $result,
            'total' => $total
        ], 200);
    }

}