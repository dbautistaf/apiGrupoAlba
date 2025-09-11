<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\TipoEstadoSolicitudEntity;
use Illuminate\Support\Facades\Cache;

class InternacionDomiciliarioCatalogoRepository
{

    public function findByListTipoEstado()
    {
        return Cache::rememberForever("catalog_domici_tipo_estado", function () {
            return TipoEstadoSolicitudEntity::orderByDesc('estado')
                ->get();
        });
    }
}
