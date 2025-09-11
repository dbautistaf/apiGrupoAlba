<?php

namespace App\Http\Controllers\PrestacionesMedicas\Repository;

use App\Models\PrestacionesMedicas\SolicitudLentesEntity;

class SolicitudLentesFilterRepository
{

    public function findByListSolicitudes($desde, $hasta)
    {
        return SolicitudLentesEntity::with(['afiliado', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->orderByDesc('id_solitud_lente')
            ->get();
    }

    public function findByListSolicitudesFechaAndAfiliado($desde, $hasta, $search)
    {
        return SolicitudLentesEntity::with(['afiliado', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->where('dni_afiliado','LIKE', $search.'%')
            ->orderByDesc('id_solitud_lente')
            ->get();
    }
    public function findByListSolicitudesFechaAndAfiliadoNombres($desde, $hasta, $search)
    {
        return SolicitudLentesEntity::with(['afiliado', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->whereHas('afiliado', function ($query) use ($search) {
                $query->where('apellidos', 'LIKE', $search . '%');
                $query->orWhere('nombre', 'LIKE', $search . '%');
            })
            ->orderByDesc('id_solitud_lente')
            ->get();
    }

    public function findBySolicitudId($id)
    {
        return SolicitudLentesEntity::with(['afiliado', 'estado'])
            ->find($id);
    }
}
