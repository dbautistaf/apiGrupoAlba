<?php

namespace App\Http\Controllers\Coseguros\Repository;

use App\Models\Coseguros\MatrizCosegurosEntity;

class MatrizCoseguroRepository
{

    public function findByListarCoseguros()
    {
        return MatrizCosegurosEntity::get();
    }

    public function findByUpdate($item)
    {
        $coseguro = MatrizCosegurosEntity::find($item->id_coseguro);
        $coseguro->monto_regimen_general = $item->monto_regimen_general;
        $coseguro->monto_monotr_autonomo = $item->monto_monotr_autonomo;
        $coseguro->monto_monotr_social = $item->monto_monotr_social;
        $coseguro->monto_sin_coseguro = $item->monto_sin_coseguro;
        $coseguro->update();
    }
}
