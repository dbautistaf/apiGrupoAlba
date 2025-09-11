<?php

namespace App\Http\Controllers\Fiscalizacion\Repository;

use App\Models\Fiscalizacion\EstadoAcuerdoEntity;

class EstadoAcuerdoRepository
{
    public function findAll()
    {
        return EstadoAcuerdoEntity::all();
    }

    public function findById($id_estado_acuerdo)
    {
        return EstadoAcuerdoEntity::find($id_estado_acuerdo);
    }
}
