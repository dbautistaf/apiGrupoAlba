<?php

namespace App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\PrestacionesMedicas\TipoPrioridadAutorizacionesEntity;
use App\Models\PrestacionesMedicas\TipoTramiteAutorizacionesEntity;
use Illuminate\Support\Facades\Cache;

class CatalogoPrestacionesMedicasRepository
{

    public function findByListTipoTramite()
    {
        return  TipoTramiteAutorizacionesEntity::orderBy('descripcion')
            ->get();
    }

    public function findByListTipoPrioridad()
    {
        return  TipoPrioridadAutorizacionesEntity::orderBy('descripcion')
            ->get();
    }

    public function findByCreateTipoTramite($params)
    {
        return  TipoTramiteAutorizacionesEntity::create([
            'descripcion' => strtoupper($params->descripcion),
            'estado' => '1'
        ]);
    }

    public function findByCreateTipoPrioridad($params)
    {
        return  TipoPrioridadAutorizacionesEntity::create([
            'descripcion' => strtoupper($params->descripcion),
            'estado' => '1'
        ]);
    }
}
