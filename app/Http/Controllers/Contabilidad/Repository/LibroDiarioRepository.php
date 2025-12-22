<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;

class LibroDiarioRepository
{

    public function findListDetalleResumenDiario($filters)
    {
        $query = AsientosContablesEntity::with(['detalle', 'detalle.planCuenta', 'periodoContable'])
            ->where('vigente', 'ACTIVO');

        if (!empty($filters->desde) && !empty($filters->hasta)) {
            $query->whereBetween('fecha_asiento', [$filters->desde, $filters->hasta]);
        }

        if (!empty($filters->id_periodo_contable)) {
            $query->where('id_periodo_contable', $filters->id_periodo_contable);
        }

        return $query->orderByDesc('id_asiento_contable')
            // ->orderByDesc('recursor')
            ->get();
    }
}
