<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Http\Controllers\Tesoreria\Repository\PagoRetencionesRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class PagoRetencionesController extends Controller
{
    protected $repository;

    public function __construct(PagoRetencionesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * GET /api/pago-retenciones/listar
     * Lista retenciones con filtros: tipo, desde, hasta, razon_social, cuit, id_retencion
     * Query params: tipo, desde, hasta, razon_social, cuit, id_retencion
     */
    public function getListarRetenciones(Request $request)
    {
        try {
            $params = (object) [
                'tipo'         => $request->query('tipo', null),
                'desde'        => $request->query('desde', null),
                'hasta'        => $request->query('hasta', null),
                'razon_social' => $request->query('razon_social', null),
                'cuit'         => $request->query('cuit', null),
                'id_retencion' => $request->query('id_retencion', null),
            ];

            $data = $this->repository->findByListRetencionesFiltroPrincipal($params);
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error listar retenciones con filtros: ' . $e->getMessage());
            return response()->json(['message' => 'Error al listar retenciones'], 500);
        }
    }

    /**
     * GET /api/pago-retenciones/{idPago}
     * Lista todas las retenciones de un pago
     */
    public function listar($idPago)
    {
        try {
            $retenciones = $this->repository->findByPago($idPago);
            return response()->json($retenciones, 200);
        } catch (\Exception $e) {
            Log::error('Error listar retenciones: ' . $e->getMessage());
            return response()->json(['message' => 'Error al listar retenciones'], 500);
        }
    }

    /**
     * GET /api/pago-retencion-regla-vigente
     * Obtiene la regla vigente para un tipo de retención
     * Query param: id_retencion
     */
    public function getReglaVigente(Request $request)
    {
        try {
            $idRetencion = $request->query('id_retencion');

            if (!$idRetencion) {
                return response()->json(['message' => 'id_retencion es requerido'], 422);
            }

            $regla = $this->repository->getReglaVigente($idRetencion);

            if (!$regla) {
                return response()->json(['message' => 'No existe regla vigente para esta retención'], 404);
            }

            return response()->json($regla, 200);
        } catch (\Exception $e) {
            Log::error('Error obtener regla vigente: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener regla vigente'], 500);
        }
    }

    /**
     * POST /api/pago-retencion
     * Crea una nueva retención
     * Body: { id_pago, id_retencion, base_imponible, observaciones? }
     */
    public function store(Request $request)
    {
        try {
            $data = $request->only(['id_pago', 'id_retencion', 'base_imponible', 'observaciones']);

            if (!$data['id_pago'] || !$data['id_retencion'] || !isset($data['base_imponible'])) {
                return response()->json(
                    ['message' => 'id_pago, id_retencion y base_imponible son requeridos'],
                    422
                );
            }

            $resultado = $this->repository->create($data);

            if ($resultado['error']) {
                $code = $resultado['code'] ?? 500;
                return response()->json(['message' => $resultado['message']], $code);
            }

            return response()->json($resultado['data'], 201);
        } catch (\Exception $e) {
            Log::error('Error crear retención: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear retención'], 500);
        }
    }

    /**
     * PUT /api/pago-retencion/{id}
     * Actualiza una retención
     * Body: { id_retencion?, base_imponible?, observaciones? }
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->only(['id_retencion', 'base_imponible', 'observaciones']);

            if (empty($data)) {
                return response()->json(['message' => 'No hay datos para actualizar'], 422);
            }

            $resultado = $this->repository->update($id, $data);

            if ($resultado['error']) {
                $code = $resultado['code'] ?? 500;
                return response()->json(['message' => $resultado['message']], $code);
            }

            return response()->json($resultado['data'], 200);
        } catch (\Exception $e) {
            Log::error('Error actualizar retención: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar retención'], 500);
        }
    }

    /**
     * DELETE /api/pago-retencion/{id}
     * Elimina una retención
     */
    public function destroy($id)
    {
        try {
            $resultado = $this->repository->delete($id);

            if ($resultado['error']) {
                $code = $resultado['code'] ?? 500;
                return response()->json(['message' => $resultado['message']], $code);
            }

            return response()->json(['message' => 'Retención eliminada exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error('Error eliminar retención: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar retención'], 500);
        }
    }
}
