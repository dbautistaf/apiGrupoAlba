<?php

namespace App\Http\Controllers\Tesoreria\Services;

use App\Models\Tesoreria\TesExtractosBancariosEntity;
use App\Models\Tesoreria\FinanceAiMatchingEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class CygnusFinanceAiController extends Controller
{
    /**
     * Endpoint para simular el motor de matching de Inteligencia Artificial
     * (Fase 1 y 2 del Roadmap)
     */
    public function ejecutarMotorMatching(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // 1. Obtener los extractos que están PENDIENTES
            $extractosPendientes = TesExtractosBancariosEntity::where('estado_conciliacion', 'PENDIENTE')
                                                              ->orWhereNull('estado_conciliacion')
                                                              ->get();
            
            $procesados = 0;
            $conciliadosAuto = 0;
            $observados = 0;

            foreach ($extractosPendientes as $extracto) {
                $procesados++;
                
                // --- LÓGICA DEL MOTOR DE MATCHING (SIMULADA PARA MVP) ---
                // Aquí en el futuro se buscarán OPs y Movimientos Internos
                // Por ahora evaluaremos en base a palabras clave y montos
                
                $score = 0;
                $estado = 'PENDIENTE';
                $reglas = [];

                $detalleUpper = strtoupper($extracto->detalle ?? '');
                $conceptoUpper = strtoupper($extracto->concepto ?? '');

                // Regla 1: CUIT Encontrado en Detalle (+50 score)
                if (preg_match('/\b\d{11}\b/', $detalleUpper)) {
                    $score += 50;
                    $reglas[] = "CUIT Detectado en extracto";
                }

                // Regla 2: Referencia no nula (+20 score)
                if (!empty($extracto->referencia) && $extracto->referencia !== '-') {
                    $score += 20;
                    $reglas[] = "Referencia bancaria presente";
                }

                // Regla 3: Identificación de Honorarios (+20 score)
                if (strpos($detalleUpper, 'HONORARIOS') !== false || strpos($conceptoUpper, 'HONORARIO') !== false) {
                    $score += 20;
                    $reglas[] = "Concepto de Honorarios detectado";
                }

                // Regla 4: Penalización (Gastos fuera de política simulados)
                if (strpos($detalleUpper, 'UBER') !== false || strpos($detalleUpper, 'GLOVO') !== false) {
                    $estado = 'OBSERVADO';
                    $score -= 30;
                    $reglas[] = "Gasto no reconocido / Fuera de política";
                }

                // DETERMINACIÓN DEL ESTADO
                if ($estado !== 'OBSERVADO') {
                    if ($score >= 90) {
                        $estado = 'CONCILIADO_AUTO';
                    } elseif ($score >= 50) {
                        $estado = 'SUGERIDO'; // Requiere clic del humano
                    }
                }

                // GUARDAR ESTADO Y SCORE EN EL EXTRACTO
                $extracto->score_matching = max(0, min(100, $score)); // Normalizar 0-100
                $extracto->estado_conciliacion = $estado;
                $extracto->save();

                // SI FUE CONCILIADO AUTOMÁTICAMENTE, GUARDAR EN LA TABLA DE MATCHING
                if ($estado === 'CONCILIADO_AUTO') {
                    $conciliadosAuto++;
                    FinanceAiMatchingEntity::create([
                        'id_extracto_bancario' => $extracto->id_extracto,
                        'tipo_origen_interno' => 'SISTEMA_LEGACY',
                        'id_origen_interno' => 0, // Placeholder hasta enlazar OPs
                        'score_obtenido' => $extracto->score_matching,
                        'reglas_cumplidas' => $reglas,
                        'fecha_matching' => now()
                    ]);
                }

                if ($estado === 'OBSERVADO') {
                    $observados++;
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Motor de Cygnus AI ejecutado correctamente.',
                'estadisticas' => [
                    'analizados' => $procesados,
                    'conciliados_auto' => $conciliadosAuto,
                    'observados' => $observados
                ]
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error en el motor de IA: ' . $th->getMessage()
            ], 500);
        }
    }
}
