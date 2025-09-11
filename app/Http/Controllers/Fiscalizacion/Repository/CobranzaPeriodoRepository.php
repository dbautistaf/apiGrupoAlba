<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\CobranzaPeriodoEntity;

class CobranzaPeriodoRepository
{
    public function findByCobranza($id_cobranza)
    {
        return CobranzaPeriodoEntity::where('id_cobranza', $id_cobranza)->get();
    }

    public function findByNueva($params)
    {
        return CobranzaPeriodoEntity::create([
            'id_cobranza' => $params->id_cobranza,
            'id_periodo' => $params->id_periodo,
            'monto_asociado' => $params->monto_asociado,
        ]);
    }
}
