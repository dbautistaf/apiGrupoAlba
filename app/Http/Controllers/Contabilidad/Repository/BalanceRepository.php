<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use App\Models\Contabilidad\DetallePlanCuentasEntity;
use App\Models\Contabilidad\TipoPlanOrganicoCuentaEntity;
use App\Models\Contabilidad\NivelesPlanCuentaEntity;
use App\Models\Contabilidad\PeriodosContablesEntity;
use Illuminate\Support\Facades\DB;

class BalanceRepository
{
    /**
     * Retorna listado de cuentas con movimientos y saldos para balance de saldo.
     * Params esperado: id_periodo_contable, id_plan_cuenta, desde, hasta, tipo_cuenta, nivel, cuentas_a_listar, saldos_a_listar
     */
    public function findByBalanceSaldo($params)
    {
        // Normalizar parámetros
        $idPeriodo = $this->nullIfEmpty($params['id_periodo_contable'] ?? null);
        $idPlan = $this->nullIfEmpty($params['id_plan_cuenta'] ?? null);
        $fechaDesde = $this->nullIfEmpty($params['desde'] ?? null);
        $fechaHasta = $this->nullIfEmpty($params['hasta'] ?? null);
        $tipoCuenta = $this->nullIfEmpty($params['tipo_cuenta'] ?? null);
        $nivel = $this->nullIfEmpty($params['nivel'] ?? null);
        $cuentasAListar = $this->nullIfEmpty($params['cuentas_a_listar'] ?? null);
        $saldosAListar = $this->nullIfEmpty($params['saldos_a_listar'] ?? null);

        // Obtener cuentas con filtros aplicados
        $q = DetallePlanCuentasEntity::query();
        $q->where('vigente', '<>', '0');

        // Filtro por plan de cuenta
        if ($idPlan) {
            $q->where('id_plan_cuenta', $idPlan);
        }

        // Filtro por tipo de cuenta
        if ($tipoCuenta && $tipoCuenta !== 'Todos') {
            // Buscar el id_tipo_cuenta por descripción
            $tipoEntity = TipoPlanOrganicoCuentaEntity::where('descripcion', $tipoCuenta)
                ->where('vigente', '<>', '0')
                ->first();
            if ($tipoEntity) {
                $q->where('id_tipo_cuenta', $tipoEntity->id_tipo_cuenta);
            }
        }

        // Filtro por nivel
        if ($nivel) {
            // Normalizar a mayúsculas para comparar correctamente
            $nivelNormalized = mb_strtoupper($nivel);
            // Buscar el id del nivel por descripción ignorando mayúsculas/minúsculas
            $nivelEntity = NivelesPlanCuentaEntity::whereRaw('UPPER(descripcion) = ?', [$nivelNormalized])
                ->where('vigente', '<>', '0')
                ->first();
            if ($nivelEntity) {
                // El campo correcto en DetallePlanCuentasEntity es id_nivel_plan_cuenta
                $q->where('id_nivel_plan_cuenta', $nivelEntity->id_tipo_nivel_plan_cuenta);
            }
        }

        // Filtro por cuentas a listar (Solo cuentas imputable)
        if ($cuentasAListar === 'Solo cuentas imputable') {
            $q->where('imputable', 1);
        }

        $cuentas = $q->orderBy('codigo_cuenta')->get();

        $resultado = [];
        foreach ($cuentas as $cuenta) {
            $movimientosCollection = $this->findMovimientosCuenta(
                $cuenta->id_detalle_plan,
                $fechaDesde,
                $fechaHasta,
                $idPeriodo
            );

            // transformar movimientos al formato esperado por frontend (mismo que libro mayor)
            $movimientos = $movimientosCollection->map(function ($m) {
                $asiento = $m->asientoContable ?? null;
                return [
                    'id_asiento_contable_detalle' => $m->id_asiento_contable_detalle,
                    'id_asiento_contable' => $m->id_asiento_contable,
                    'fecha_asiento' => $asiento->fecha_asiento ?? null,
                    'numero' => $asiento->numero ?? null,
                    'asiento_modelo' => $asiento->asiento_modelo ?? null,
                    'asiento_leyenda' => $asiento->asiento_leyenda ?? null,
                    'monto_debe' => (float) $m->monto_debe,
                    'monto_haber' => (float) $m->monto_haber,
                    'observaciones' => $m->observaciones,
                    'id_detalle_plan' => $m->id_detalle_plan,
                    'codigo_cuenta' => $m->planCuenta->codigo_cuenta ?? null,
                    'nombre_cuenta' => $m->planCuenta->cuenta ?? null,
                ];
            })->values();

            $totalDebe = $movimientos->sum('monto_debe');
            $totalHaber = $movimientos->sum('monto_haber');

            $saldoAnterior = $this->findSaldoAnterior($cuenta->id_detalle_plan, $fechaDesde, $idPeriodo);
            $saldoAnteriorValor = $saldoAnterior['saldo_anterior'] ?? 0.0;
            $saldoFinal = $saldoAnteriorValor + ($totalDebe - $totalHaber);

            // Filtrar según saldos_a_listar
            if ($saldosAListar === 'Cuentas con movimiento') {
                if ($movimientos->count() === 0 && abs($saldoAnteriorValor) < 0.0001 && abs($saldoFinal) < 0.0001) {
                    continue;
                }
            }

            $resultado[] = [
                'id_detalle_plan' => $cuenta->id_detalle_plan,
                'codigo_cuenta' => $cuenta->codigo_cuenta,
                'nombre_cuenta' => $cuenta->cuenta,
                'saldo_anterior' => $saldoAnterior, // mantiene formato {total_debe,total_haber,saldo_anterior}
                'movimientos' => $movimientos->toArray(),
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'saldo_final' => $saldoFinal
            ];
        }

        return $resultado;
    }

    /**
     * Busca movimientos de una cuenta en rango (usa JOIN para filtrar por asientos)
     */
    private function findMovimientosCuenta($idDetallePlan, $fechaDesde = null, $fechaHasta = null, $idPeriodoContable = null)
    {
        $query = DetalleAsientosContablesEntity::select('tb_cont_asientos_contables_detalle.*')
            ->join('tb_cont_asientos_contables as ac', 'tb_cont_asientos_contables_detalle.id_asiento_contable', '=', 'ac.id_asiento_contable')
            ->where('tb_cont_asientos_contables_detalle.id_detalle_plan', $idDetallePlan)
            ->whereIn('ac.vigente', ['ACTIVO', 'S', '1', 'CONTRAASIENTO']);

        // Si vienen las fechas, aplicamos el filtro de rango
        if (!is_null($fechaDesde) && !is_null($fechaHasta)) {
            $query->whereBetween('ac.fecha_asiento', [$fechaDesde, $fechaHasta]);
        }

        // Si viene el periodo contable, aplicamos la lógica de filtrado
        if (!is_null($idPeriodoContable)) {
            // Buscar el periodo contable para determinar si es anual o mensual
            $periodoContable = PeriodosContablesEntity::find($idPeriodoContable);

            if ($periodoContable) {
                if ($periodoContable->id_tipo_periodo === 1) {
                    // Periodo mensual: filtrar por el id_periodo_contable específico
                    $query->where('ac.id_periodo_contable', $idPeriodoContable);
                } elseif ($periodoContable->id_tipo_periodo === 2) {
                    // Periodo anual: filtrar por todos los asientos del año usando join
                    $anio = $periodoContable->anio_periodo;
                    $query->join('tb_cont_periodos_contables as pc_anio', 'ac.id_periodo_contable', '=', 'pc_anio.id_periodo_contable')
                        ->where('pc_anio.anio_periodo', $anio);
                }
            }
        }

        $detalles = $query->orderBy('ac.fecha_asiento')->orderBy('ac.numero')->get();
        // eager load
        $detalles->load(['asientoContable', 'planCuenta']);
        return $detalles;
    }

    /**
     * Calcula saldo anterior (debe - haber) para la cuenta antes de fechaDesde
     */
    private function findSaldoAnterior($idDetallePlan, $fechaDesde = null, $idPeriodoContable = null)
    {
        $query = DetalleAsientosContablesEntity::join('tb_cont_asientos_contables as ac', 'tb_cont_asientos_contables_detalle.id_asiento_contable', '=', 'ac.id_asiento_contable')
            ->where('tb_cont_asientos_contables_detalle.id_detalle_plan', $idDetallePlan)
            ->whereIn('ac.vigente', ['ACTIVO', 'S', '1', 'CONTRAASIENTO']);

        // Si tenemos período contable, usar la fecha de inicio del período
        if (!is_null($idPeriodoContable)) {
            $periodoContable = PeriodosContablesEntity::find($idPeriodoContable);

            if ($periodoContable) {
                if ($periodoContable->id_tipo_periodo === 1) {
                    // Período mensual: saldo anterior = todo antes del inicio del período
                    $query->where('ac.fecha_asiento', '<', $periodoContable->periodo_inicio);
                } elseif ($periodoContable->id_tipo_periodo === 2) {
                    // Período anual: saldo anterior = todo antes del año
                    $anio = $periodoContable->anio_periodo;
                    $query->where('ac.fecha_asiento', '<', $anio . '-01-01');
                }
            }
        } else {
            // Si no hay período, usar la fecha proporcionada
            if (!is_null($fechaDesde)) {
                $query->where('ac.fecha_asiento', '<', $fechaDesde);
            } else {
                // Sin fecha ni período, retornar saldo cero
                return [
                    'total_debe' => 0.0,
                    'total_haber' => 0.0,
                    'saldo_anterior' => 0.0
                ];
            }
        }

        $saldos = $query->selectRaw('SUM(monto_debe) as total_debe, SUM(monto_haber) as total_haber')->first();

        $totalDebe = $saldos->total_debe ?? 0.0;
        $totalHaber = $saldos->total_haber ?? 0.0;

        return [
            'total_debe' => (float) $totalDebe,
            'total_haber' => (float) $totalHaber,
            'saldo_anterior' => (float) ($totalDebe - $totalHaber)
        ];
    }

    private function nullIfEmpty($v)
    {
        if (is_null($v))
            return null;
        if (is_string($v)) {
            $t = trim($v);
            return $t === '' ? null : $t;
        }
        return $v;
    }

    /**
     * Estructura lista para exportar a PDF/Excel (mismo formato que findByBalanceSaldo pero con totales)
     */
    public function findByReporteBalance($params, $empresa = null)
    {
        $cuentas = $this->findByBalanceSaldo($params);

        $reporte = [
            'encabezado' => [
                'empresa' => $empresa ?? ['nombre' => 'Mi Empresa'],
                'filtros' => [
                    'id_periodo_contable' => $params['id_periodo_contable'] ?? null,
                    'anio_periodo' => $params['anio_periodo'] ?? null,
                    'periodo_contable' => $params['periodo_contable'] ?? null,
                    'desde' => $params['desde_reporte'] ?? $params['desde'] ?? null,
                    'hasta' => $params['hasta_reporte'] ?? $params['hasta'] ?? null,
                    'tipo_cuenta' => $params['tipo_cuenta'] ?? null,
                    'nivel' => $params['nivel'] ?? null,
                    'cuentas_a_listar' => $params['cuentas_a_listar'] ?? null,
                    'saldos_a_listar' => $params['saldos_a_listar'] ?? null,
                ],
                'fecha_generacion' => date('Y-m-d H:i:s')
            ],
            'cuentas' => [],
            'totales' => [
                'total_debe' => 0.0,
                'total_haber' => 0.0,
                'total_saldo_anterior' => 0.0,
                'diferencia' => 0.0
            ]
        ];

        foreach ($cuentas as $c) {
            $saldoAnteriorValor = is_array($c['saldo_anterior']) ? ($c['saldo_anterior']['saldo_anterior'] ?? 0) : ($c['saldo_anterior'] ?? 0);
            $reporte['cuentas'][] = [
                'id_detalle_plan' => $c['id_detalle_plan'],
                'codigo_cuenta' => $c['codigo_cuenta'],
                'nombre_cuenta' => $c['nombre_cuenta'],
                'saldo_anterior' => $saldoAnteriorValor,
                'movimientos' => $c['movimientos'],
                'total_debe' => $c['total_debe'],
                'total_haber' => $c['total_haber'],
                'saldo_final' => $c['saldo_final']
            ];
            $reporte['totales']['total_debe'] += $c['total_debe'];
            $reporte['totales']['total_haber'] += $c['total_haber'];
            $reporte['totales']['total_saldo_anterior'] += $saldoAnteriorValor;
        }

        $reporte['totales']['diferencia'] = $reporte['totales']['total_debe'] - $reporte['totales']['total_haber'];

        return $reporte;
    }
}