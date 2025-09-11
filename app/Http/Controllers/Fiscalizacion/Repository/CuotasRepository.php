<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\CuotasEntity;

class CuotasRepository
{
    public function findByAcuerdo($id_acuerdo_pago)
    {
        return CuotasEntity::where('id_acuerdo_pago', $id_acuerdo_pago)->get();
    }

    public function findByNueva($params)
    {
        return CuotasEntity::create([
            'id_acuerdo_pago' => $params->id_acuerdo_pago,
            'periodo' => $params->periodo,
            'importe' => $params->importe,
            'fecha_pago' => $params->fecha_pago,
            'comprobante' => $params->comprobante,
            'estado' => $params->estado,
        ]);
    }
}
