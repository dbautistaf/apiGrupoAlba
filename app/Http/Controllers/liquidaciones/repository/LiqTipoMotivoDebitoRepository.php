<?php

namespace App\Http\Controllers\liquidaciones\repository;
use App\Models\liquidaciones\LiqTipoMotivoDebitoEntity;

class LiqTipoMotivoDebitoRepository
{

    public function findByListDescripcion($search, $vigente, $top)
    {
        return LiqTipoMotivoDebitoEntity::where('vigente', $vigente)
            ->where('descripcion_motivo', 'LIKE', '%' . $search . '%')
            ->limit($top)
            ->get();
    }
}
