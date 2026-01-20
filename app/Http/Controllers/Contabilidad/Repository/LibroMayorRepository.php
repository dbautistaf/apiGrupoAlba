<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use App\Models\Contabilidad\DetallePlanCuentasEntity;
use App\Models\Contabilidad\PeriodosContablesEntity;
use Illuminate\Support\Facades\DB;

class LibroMayorRepository
{

    public function findByLibroMayor($params)
    {
        $query = DetalleAsientosContablesEntity::with([
            'asientoContable.tipo',
            'planCuenta',
            'proveedorCuentaContable.proveedor',
            'formaPagoCuentaContable.formaPago'
        ])
            ->join('tb_cont_asientos_contables as ac', 'tb_cont_asientos_contables_detalle.id_asiento_contable', '=', 'ac.id_asiento_contable')
            ->join('tb_cont_detalle_plan_cuentas as pc', 'tb_cont_asientos_contables_detalle.id_detalle_plan', '=', 'pc.id_detalle_plan')
            // aceptar distintos valores históricos para "activo"
            ->whereIn('ac.vigente', ['ACTIVO', 'S', '1']);

        // Filtro por período contable
        if (!is_null($params->id_periodo_contable)) {
            $query->where('ac.id_periodo_contable', $params->id_periodo_contable);
        }

        // Filtro por rango de fechas
        if (!is_null($params->desde) && !is_null($params->hasta)) {
            $query->whereBetween('ac.fecha_asiento', [$params->desde, $params->hasta]);
        }

        // Filtro por rango de códigos de cuenta
        if (!is_null($params->codigo_desde) && !is_null($params->codigo_hasta)) {
            $query->whereBetween('pc.codigo_cuenta', [$params->codigo_desde, $params->codigo_hasta]);
        } elseif (!is_null($params->codigo_desde)) {
            $query->where('pc.codigo_cuenta', '>=', $params->codigo_desde);
        } elseif (!is_null($params->codigo_hasta)) {
            $query->where('pc.codigo_cuenta', '<=', $params->codigo_hasta);
        }

        // Filtro por cuenta específica
        if (!is_null($params->id_detalle_plan_desde)) {
            $query->where('tb_cont_asientos_contables_detalle.id_detalle_plan', $params->id_detalle_plan_desde);
        }

        $query->orderBy('pc.codigo_cuenta')
            ->orderBy('ac.fecha_asiento')
            ->orderBy('ac.numero');

        return $query->get();
    }

    private function findByMovimientosCuenta($idDetallePlan, $fechaDesde, $fechaHasta, $idPeriodoContable = null)
    {
        // Normalizar fechas/periodo (tratar '' como null)
        if ($fechaDesde === '')
            $fechaDesde = null;
        if ($fechaHasta === '')
            $fechaHasta = null;
        if ($idPeriodoContable === '')
            $idPeriodoContable = null;

        // Usar JOIN para filtrar y ordenar por la tabla de asientos (ac)
        $query = DetalleAsientosContablesEntity::select('tb_cont_asientos_contables_detalle.*')
            ->join('tb_cont_asientos_contables as ac', 'tb_cont_asientos_contables_detalle.id_asiento_contable', '=', 'ac.id_asiento_contable')
            ->where('tb_cont_asientos_contables_detalle.id_detalle_plan', $idDetallePlan)
            // aceptar distintos valores históricos para "vigente" en asientos
            ->whereIn('ac.vigente', ['ACTIVO', 'S', '1', 'CONTRAASIENTO'])
        ;

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

        // incluir relaciones para el mapeo posterior
        $query = $query->orderBy('ac.fecha_asiento')->orderBy('ac.numero');

        // obtener detalles luego cargar relaciones para facilitar consumo
        $detalles = $query->get();

        // eager load relations on collection
        $detalles->load(['asientoContable', 'planCuenta', 'proveedorCuentaContable.proveedor', 'formaPagoCuentaContable.formaPago']);

        return $detalles;
    }

    public function findBySaldoAnterior($idDetallePlan, $fechaDesde, $idPeriodoContable = null)
    {
        $query = DetalleAsientosContablesEntity::join('tb_cont_asientos_contables as ac', 'tb_cont_asientos_contables_detalle.id_asiento_contable', '=', 'ac.id_asiento_contable')
            ->where('tb_cont_asientos_contables_detalle.id_detalle_plan', $idDetallePlan)
            ->whereIn('ac.vigente', ['ACTIVO', 'S', '1']);

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
            if (!is_null($fechaDesde) && $fechaDesde !== '') {
                $query->where('ac.fecha_asiento', '<', $fechaDesde);
            } else {
                // Sin fecha ni período, no hay saldo anterior
                return [
                    'total_debe' => 0,
                    'total_haber' => 0,
                    'saldo_anterior' => 0
                ];
            }
        }

        $saldos = $query->selectRaw('
            SUM(monto_debe) as total_debe,
            SUM(monto_haber) as total_haber
        ')->first();

        return [
            'total_debe' => $saldos->total_debe ?? 0,
            'total_haber' => $saldos->total_haber ?? 0,
            'saldo_anterior' => ($saldos->total_debe ?? 0) - ($saldos->total_haber ?? 0)
        ];
    }

    public function findByPlanesCuentaEnRango($codigoDesde = null, $codigoHasta = null)
    {
        // aceptar cuentas cuyo campo 'vigente' no sea '0' (varios valores históricos posibles)
        $query = DetallePlanCuentasEntity::where('vigente', '<>', '0');

        if (!is_null($codigoDesde) && !is_null($codigoHasta)) {
            $query->whereBetween('codigo_cuenta', [$codigoDesde, $codigoHasta]);
        } elseif (!is_null($codigoDesde)) {
            $query->where('codigo_cuenta', '>=', $codigoDesde);
        } elseif (!is_null($codigoHasta)) {
            $query->where('codigo_cuenta', '<=', $codigoHasta);
        }

        return $query->orderBy('codigo_cuenta')->get();
    }

    public function findByResumenPorCuenta($params)
    {
        // Normalizar parámetros (tratar '' como null)
        $codigoDesde = isset($params->codigo_desde) ? trim($params->codigo_desde) : null;
        $codigoHasta = isset($params->codigo_hasta) ? trim($params->codigo_hasta) : null;
        $idDesde = isset($params->id_detalle_plan_desde) ? trim($params->id_detalle_plan_desde) : null;
        $idHasta = isset($params->id_detalle_plan_hasta) ? trim($params->id_detalle_plan_hasta) : null;
        $fechaDesde = isset($params->desde) ? trim($params->desde) : null;
        $fechaHasta = isset($params->hasta) ? trim($params->hasta) : null;
        $idPeriodo = isset($params->id_periodo_contable) ? trim($params->id_periodo_contable) : null;
        $saldoAnteriorFlag = isset($params->saldo_anterior) ? trim($params->saldo_anterior) : null;

        if ($codigoDesde === '')
            $codigoDesde = null;
        if ($codigoHasta === '')
            $codigoHasta = null;
        if ($idDesde === '')
            $idDesde = null;
        if ($idHasta === '')
            $idHasta = null;
        if ($fechaDesde === '')
            $fechaDesde = null;
        if ($fechaHasta === '')
            $fechaHasta = null;
        if ($idPeriodo === '')
            $idPeriodo = null;
        if ($saldoAnteriorFlag === '')
            $saldoAnteriorFlag = null;

        // Obtener lista de cuentas según filtros: por id_detalle_plan (desde/hasta) o por codigo (desde/hasta)
        if (!empty($idDesde) || !empty($idHasta)) {
            $q = DetallePlanCuentasEntity::where('vigente', '<>', '0');
            if (!empty($idDesde) && !empty($idHasta)) {
                $q->whereBetween('id_detalle_plan', [$idDesde, $idHasta]);
            } elseif (!empty($idDesde)) {
                $q->where('id_detalle_plan', $idDesde);
            } elseif (!empty($idHasta)) {
                $q->where('id_detalle_plan', $idHasta);
            }
            $cuentas = $q->orderBy('codigo_cuenta')->get();
        } else {
            $cuentas = $this->findByPlanesCuentaEnRango($codigoDesde, $codigoHasta);
        }

        $resultado = [];

        foreach ($cuentas as $cuenta) {
            // Obtener movimientos de la cuenta (colección de modelos)
            $movimientosCollection = $this->findByMovimientosCuenta(
                $cuenta->id_detalle_plan,
                $fechaDesde,
                $fechaHasta,
                $idPeriodo
            );

            // Transformar movimientos al formato simple que espera el frontend
            $movimientos = $movimientosCollection->map(function ($m) {
                $asiento = $m->asientoContable ?? null;
                return [
                    'id_asiento_contable_detalle' => $m->id_asiento_contable_detalle,
                    'id_asiento_contable' => $m->id_asiento_contable,
                    'fecha_asiento' => $asiento ? (string) $asiento->fecha_asiento : null,
                    'numero' => $asiento ? $asiento->numero : null,
                    'asiento_modelo' => $asiento ? $asiento->asiento_modelo : null,
                    'asiento_leyenda' => $asiento ? $asiento->asiento_leyenda : null,
                    'monto_debe' => (float) $m->monto_debe,
                    'monto_haber' => (float) $m->monto_haber,
                    'observaciones' => $m->observaciones,
                    'id_detalle_plan' => $m->id_detalle_plan,
                    'proveedor' => $m->proveedorCuentaContable && $m->proveedorCuentaContable->proveedor
                        ? ($m->proveedorCuentaContable->proveedor->razon_social ?? $m->proveedorCuentaContable->proveedor->nombre ?? null)
                        : null,
                    'codigo_cuenta' => $m->planCuenta->codigo_cuenta ?? null,
                    'nombre_cuenta' => $m->planCuenta->cuenta ?? null,
                    'id_proveedor_cuenta_contable' => $m->id_proveedor_cuenta_contable,
                    'id_forma_pago_cuenta_contable' => $m->id_forma_pago_cuenta_contable
                ];
            })->values();

            // Calcular totales del rango
            $totalDebe = $movimientos->sum('monto_debe');
            $totalHaber = $movimientos->sum('monto_haber');

            // Calcular saldo anterior si se solicitó
            $saldoAnterior = null;
            if ($saldoAnteriorFlag === 'SI') {
                // Para saldo anterior con período, usar la fecha de inicio del período o la fecha proporcionada
                $fechaParaSaldo = $fechaDesde;

                if (!is_null($idPeriodo) && (is_null($fechaDesde) || $fechaDesde === '')) {
                    // Si tenemos período pero no fecha, calcular saldo anterior con período
                    $saldoAnterior = $this->findBySaldoAnterior(
                        $cuenta->id_detalle_plan,
                        null,
                        $idPeriodo
                    );
                } else {
                    // Si tenemos fecha o ambos, usar la fecha
                    $saldoAnterior = $this->findBySaldoAnterior(
                        $cuenta->id_detalle_plan,
                        $fechaParaSaldo,
                        $idPeriodo
                    );
                }
            }

            $saldoAnteriorValor = $saldoAnterior['saldo_anterior'] ?? 0;
            $saldoActual = $saldoAnteriorValor + ($totalDebe - $totalHaber);

            // Solo incluir si tiene movimientos o saldo anterior distinto de cero
            if ($movimientos->count() > 0 || ($saldoAnterior && ($saldoAnteriorValor != 0))) {
                $resultado[] = [
                    'id_detalle_plan' => $cuenta->id_detalle_plan,
                    'codigo_cuenta' => $cuenta->codigo_cuenta,
                    'nombre_cuenta' => $cuenta->cuenta,
                    'saldo_anterior' => $saldoAnterior,
                    'movimientos' => $movimientos->toArray(),
                    'total_debe' => $totalDebe,
                    'total_haber' => $totalHaber,
                    'saldo_final' => $saldoActual
                ];
            }
        }

        return $resultado;
    }

    public function findByReporteLibroMayor($params, $empresa = null)
    {
        // Obtener resumen por cuenta (usa filtros ya saneados en el request)
        $cuentas = $this->findByResumenPorCuenta($params);

        $reporte = [
            'encabezado' => [
                'empresa' => $empresa ?? [
                    'nombre' => 'Mi Empresa',
                    'cuit' => ''
                ],
                'filtros' => [
                    'id_periodo_contable' => $params->id_periodo_contable ?? null,
                    'desde' => $params->desde ?? null,
                    'hasta' => $params->hasta ?? null,
                    'saldo_anterior' => $params->saldo_anterior ?? 'NO',
                    'codigo_desde' => $params->codigo_desde ?? null,
                    'codigo_hasta' => $params->codigo_hasta ?? null
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
            // cada $c tiene: id_detalle_plan, codigo_cuenta, nombre_cuenta, saldo_anterior, movimientos (array), total_debe, total_haber, saldo_final
            $movimientos = [];
            $saldoAnteriorValor = 0.0;
            if (!empty($c['saldo_anterior'])) {
                $saldoAnteriorValor = $c['saldo_anterior']['saldo_anterior'] ?? 0.0;
            }

            foreach ($c['movimientos'] as $m) {
                $movimientos[] = [
                    'fecha' => $m['fecha_asiento'],
                    'numero' => $m['numero'],
                    'modelo' => $m['asiento_modelo'],
                    'leyenda' => $m['asiento_leyenda'],
                    'observaciones' => $m['observaciones'],
                    'debe' => (float) $m['monto_debe'],
                    'haber' => (float) $m['monto_haber']
                ];
            }

            $totalDebe = (float) ($c['total_debe'] ?? array_sum(array_column($movimientos, 'debe')));
            $totalHaber = (float) ($c['total_haber'] ?? array_sum(array_column($movimientos, 'haber')));
            $saldoFinal = $saldoAnteriorValor + ($totalDebe - $totalHaber);

            $reporte['cuentas'][] = [
                'id_detalle_plan' => $c['id_detalle_plan'],
                'codigo_cuenta' => $c['codigo_cuenta'],
                'nombre_cuenta' => $c['nombre_cuenta'],
                'saldo_anterior' => $saldoAnteriorValor,
                'movimientos' => $movimientos,
                'total_debe' => $totalDebe,
                'total_haber' => $totalHaber,
                'saldo_final' => $saldoFinal
            ];

            $reporte['totales']['total_debe'] += $totalDebe;
            $reporte['totales']['total_haber'] += $totalHaber;
            $reporte['totales']['total_saldo_anterior'] += $saldoAnteriorValor;
        }

        $reporte['totales']['diferencia'] = $reporte['totales']['total_debe'] - $reporte['totales']['total_haber'];

        return $reporte;
    }
}
