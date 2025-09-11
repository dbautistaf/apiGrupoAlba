<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\AsientosContablesEntity;
use App\Models\Contabilidad\DetalleAsientosContablesEntity;

class LibroDiarioRepository
{

    public function findListDetalleResumenDiario($filters)
    {
        return AsientosContablesEntity::with(['detalle', 'detalle.planCuenta'])
            ->where('vigente', 'ACTIVO')
            ->whereBetween('fecha_asiento', [$filters->desde, $filters->hasta])
            ->where('id_periodo_contable', $filters->id_periodo_contable)
            ->orderByDesc('id_asiento_contable')
            // ->orderByDesc('recursor')
            ->get();
    }
}
