<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\AcuerdoPagoPeriodoEntity;
use Illuminate\Support\Facades\DB;

class AcuerdoPagoPeriodoRepository
{
    public function findByAcuerdo($id_acuerdo_pago)
    {
        return AcuerdoPagoPeriodoEntity::where('id_acuerdo_pago', $id_acuerdo_pago)->get();
    }

    public function findByNueva($params)
    {
        return AcuerdoPagoPeriodoEntity::create([
            'id_acuerdo_pago' => $params->id_acuerdo_pago,
            'id_periodo' => $params->id_periodo,
            'monto_asociado' => $params->monto_asociado,
        ]);
    }
}
