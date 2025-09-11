<?php

namespace App\Http\Controllers\Utils;

use App\Models\Administracion\CorrelativosOspfEntity;
use App\Models\Internaciones\InternacionesEntity;

class CorrelativosOspfRepository
{

    public function findByObtenerCorrelativo($tipo)
    {

        $contar = InternacionesEntity::orderBy('cod_internacion', 'desc')->first();

        if ($contar == null) {
            $correlativo = CorrelativosOspfEntity::find($tipo);

            if ($correlativo == null) {
                return null; 
            }

            return ($correlativo->numero + 1);
        } else {
            return ($contar->num_internacion + 1);
        }
    }

    public function findByObtenerCorrelativoAndAbreviatura($tipo)
    {
        $correlativo = CorrelativosOspfEntity::find($tipo);

        if ($correlativo == null) {
            return null;
        }

        return $correlativo->abreviatura . '-' . ($correlativo->numero + 1);
    }

    public function findByIncrementarCorrelativo($tipo)
    {
        $correlativo = CorrelativosOspfEntity::find($tipo);
        $correlativo->numero += 1;
        $correlativo->update();
        $correlativo->refresh();
        return $correlativo;
    }
}
