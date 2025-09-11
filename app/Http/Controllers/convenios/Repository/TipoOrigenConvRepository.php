<?php
namespace App\Http\Controllers\convenios\Repository;

use App\Models\convenios\ConvenioTipoOrigenEntity;

class TipoOrigenConvRepository
{

    public function findByListAlls($vigente)
    {
        return ConvenioTipoOrigenEntity::where('vigente', $vigente)->get();
    }
}
