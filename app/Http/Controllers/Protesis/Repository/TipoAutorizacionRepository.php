<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\ProtesisTipoAutorizacionEntity;

class TipoAutorizacionRepository
{

    public function findByListVigente($estado)
    {
        return ProtesisTipoAutorizacionEntity::where('vigente', $estado)
            ->orderBy('descripcion')
            ->get();
    }
}
