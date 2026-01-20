<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;
use App\Models\Contabilidad\PeriodosContablesEntity;

class LibroDiarioRepository
{

    public function findListDetalleResumenDiario($filters)
    {
        $query = AsientosContablesEntity::with(['detalle', 'detalle.planCuenta', 'periodoContable'])
            ->where('vigente', 'ACTIVO' || 'CONTRAASIENTO');

        // Si vienen las fechas, aplicamos el filtro de rango
        if (isset($filters->desde) && isset($filters->hasta) && !empty($filters->desde) && !empty($filters->hasta)) {
            $query->whereBetween('fecha_asiento', [$filters->desde, $filters->hasta]);
        }

        // Si viene el periodo contable, lo aplicamos al filtro
        if (isset($filters->id_periodo_contable) && !empty($filters->id_periodo_contable)) {
            // Buscar el periodo contable para determinar si es anual o mensual
            $periodoContable = PeriodosContablesEntity::find($filters->id_periodo_contable);

            if ($periodoContable) {
                if ($periodoContable->id_tipo_periodo === 1) {
                    // Periodo mensual: filtrar por el id_periodo_contable específico
                    $query->where('id_periodo_contable', $filters->id_periodo_contable);
                } elseif ($periodoContable->id_tipo_periodo === 2) {
                    // Periodo anual: filtrar por todos los asientos del año
                    $anio = $periodoContable->anio_periodo;
                    $query->whereHas('periodoContable', function ($q) use ($anio) {
                        $q->where('anio_periodo', $anio);
                    });
                }
            }
        }

        $query->orderByDesc('id_asiento_contable');

        // Si vienen parámetros de paginación, aplicarlos
        if (isset($filters->page) && isset($filters->limit)) {
            return $query->paginate($filters->limit, ['*'], 'page', $filters->page);
        }

        // Si no hay paginación, devolver todos los resultados
        return $query->get();
    }

    public function getTotalCount($filters)
    {
        $query = AsientosContablesEntity::where('vigente', 'ACTIVO' || 'CONTRAASIENTO');

        // Si vienen las fechas, aplicamos el filtro de rango
        if (isset($filters->desde) && isset($filters->hasta) && !empty($filters->desde) && !empty($filters->hasta)) {
            $query->whereBetween('fecha_asiento', [$filters->desde, $filters->hasta]);
        }

        // Si viene el periodo contable, lo aplicamos al filtro
        if (isset($filters->id_periodo_contable) && !empty($filters->id_periodo_contable)) {
            $periodoContable = PeriodosContablesEntity::find($filters->id_periodo_contable);

            if ($periodoContable) {
                if ($periodoContable->id_tipo_periodo === 1) {
                    $query->where('id_periodo_contable', $filters->id_periodo_contable);
                } elseif ($periodoContable->id_tipo_periodo === 2) {
                    $anio = $periodoContable->anio_periodo;
                    $query->whereHas('periodoContable', function ($q) use ($anio) {
                        $q->where('anio_periodo', $anio);
                    });
                }
            }
        }

        return $query->count();
    }
}
