<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\SaldosRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SaldosController extends Controller
{
    private $saldosRepository;

    public function __construct(SaldosRepository $saldosRepository)
    {
        $this->saldosRepository = $saldosRepository;
    }

    /**
     * Lista proveedores y prestadores con sus deudas pendientes
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListarProveedoresPrestadoresConDeudas(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validar parámetros de entrada
            $filtros = [];

            if ($request->filled('cuit')) {
                $filtros['cuit'] = trim($request->cuit);
            }

            if ($request->filled('razon_social')) {
                $filtros['razon_social'] = trim($request->razon_social);
            }

            if ($request->filled('tipo')) {
                $tipo = strtoupper(trim($request->tipo));
                if (in_array($tipo, ['PROVEEDOR', 'PRESTADOR'])) {
                    $filtros['tipo'] = $tipo;
                } else {
                    return response()->json([
                        'message' => 'El tipo debe ser "PROVEEDOR" o "PRESTADOR"'
                    ], 400);
                }
            }

            // Obtener número de elementos por página (default: 10, máximo: 100)
            $perPage = $request->input('per_page', 10);
            $perPage = min(max($perPage, 1), 100);

            /** @var LengthAwarePaginator $resultados */
            $resultados = $this->saldosRepository->getProveedoresPrestadoresConDeudas($filtros, $perPage);

            return response()->json([
                'success' => true,
                'data' => $resultados->items(),
                'pagination' => [
                    'current_page' => $resultados->currentPage(),
                    'per_page' => $resultados->perPage(),
                    'total' => $resultados->total(),
                    'last_page' => $resultados->lastPage(),
                    'from' => $resultados->firstItem(),
                    'to' => $resultados->lastItem()
                ],
                'filtros_aplicados' => $filtros,
                'message' => 'Saldos de proveedores y prestadores obtenidos correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener saldos de proveedores y prestadores: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al obtener los saldos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el detalle de facturas pendientes de un proveedor o prestador específico
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetalleFacturasPendientes(Request $request)
    {
        try {
            // Validar parámetros requeridos
            $tipo = strtoupper(trim($request->input('tipo', '')));
            $id = $request->input('id');

            if (!in_array($tipo, ['PROVEEDOR', 'PRESTADOR'])) {
                return response()->json([
                    'message' => 'El parámetro "tipo" es requerido y debe ser "PROVEEDOR" o "PRESTADOR"'
                ], 400);
            }

            if (!$id || !is_numeric($id)) {
                return response()->json([
                    'message' => 'El parámetro "id" es requerido y debe ser numérico'
                ], 400);
            }

            // Obtener número de elementos por página
            $perPage = $request->input('per_page', 10);
            $perPage = min(max($perPage, 1), 100);

            $facturasPendientes = $this->saldosRepository->getDetalleFacturasPendientes($tipo, $id, $perPage);

            return response()->json([
                'success' => true,
                'data' => $facturasPendientes->items(),
                'pagination' => [
                    'current_page' => $facturasPendientes->currentPage(),
                    'per_page' => $facturasPendientes->perPage(),
                    'total' => $facturasPendientes->total(),
                    'last_page' => $facturasPendientes->lastPage(),
                    'from' => $facturasPendientes->firstItem(),
                    'to' => $facturasPendientes->lastItem()
                ],
                'parametros' => [
                    'tipo' => $tipo,
                    'id' => $id
                ],
                'message' => sprintf('Facturas pendientes del %s obtenidas correctamente', strtolower($tipo))
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener detalle de facturas pendientes: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al obtener el detalle de facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene resumen general de deudas por tipo
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResumenDeudas()
    {
        try {
            $resumen = $this->saldosRepository->getResumenDeudasPorTipo();

            return response()->json([
                'success' => true,
                'data' => $resumen,
                'message' => 'Resumen de deudas obtenido correctamente'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error al obtener resumen de deudas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al obtener el resumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
