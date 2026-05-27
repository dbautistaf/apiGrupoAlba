<?php

namespace App\Http\Controllers\Contabilidad\Services;

use App\Http\Controllers\Contabilidad\Repository\RetencionReglaRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class RetencionReglasController extends Controller
{
    /**
     * Obtiene las reglas de retención vigentes para una fecha específica
     * 
     * @param Request $request
     * @param RetencionReglaRepository $repo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReglasVigentesPorFecha(Request $request, RetencionReglaRepository $repo)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fecha' => 'required|date|date_format:Y-m-d',
                'id_retencion' => 'nullable|integer|exists:tb_cont_tipo_retenciones,id_retencion'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reglas = $repo->findByReglasVigentesPorFecha(
                $request->fecha,
                $request->id_retencion
            );

            return response()->json([
                'success' => true,
                'data' => $reglas,
                'message' => 'Reglas de retención obtenidas exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene una regla específica vigente para un tipo de retención y fecha
     * 
     * @param Request $request
     * @param RetencionReglaRepository $repo
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReglaPorTipoYFecha(Request $request, RetencionReglaRepository $repo)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fecha' => 'required|date|date_format:Y-m-d',
                'id_retencion' => 'required|integer|exists:tb_cont_tipo_retenciones,id_retencion'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            $regla = $repo->findRetencionVigentePorTipoYFecha(
                $request->id_retencion,
                $request->fecha
            );

            if (!$regla) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró regla vigente para el tipo de retención y fecha especificados'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $regla,
                'message' => 'Regla de retención obtenida exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

}