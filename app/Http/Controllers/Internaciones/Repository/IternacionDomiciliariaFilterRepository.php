<?php

namespace App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\InternacionDomiciliariaEntity;
use App\Models\Internaciones\InternacionDomiciliariaHistorialCostoEntity;
use App\Models\Internaciones\InternacionDomiciliariaServiciosEntity;

class IternacionDomiciliariaFilterRepository
{

    public function findByListServiciosTop($top)
    {
        return InternacionDomiciliariaServiciosEntity::orderByDesc('id_servicio')
            ->limit($top)
            ->get();
    }

    public function findByListServiciosTipoTop($search, $top)
    {
        return InternacionDomiciliariaServiciosEntity::where('tipo_servicio', 'LIKE', $search . '%')
            ->orderByDesc('id_servicio')
            ->limit($top)
            ->get();
    }

    public function finByListSolicitudesAndLimit($desde, $hasta, $limit)
    {
        return InternacionDomiciliariaEntity::with(['afiliado', 'estado', 'detalle', 'detalle.servicio'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->orderByDesc('id_internacion_domiciliaria')
            ->limit($limit)
            ->get();
    }

    public function findBySolicitudId($id)
    {
        return InternacionDomiciliariaEntity::with(['afiliado', 'estado', 'detalle', 'detalle.servicio'])
            ->find($id);
    }

    public function findByListHistorialCostoBetween($id)
    {
        return InternacionDomiciliariaHistorialCostoEntity::with(['servicio'])
            ->where('id_internacion_domiciliaria', $id)
            ->orderByDesc('id_historial')
            ->get();
    }
}
