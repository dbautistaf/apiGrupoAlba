<?php

namespace App\Http\Controllers\Derivacion\Repository;

use App\Models\Derivaciones\DerivacionEntity;

class DerivacionFiltersRepository
{
    public function findByListBetween($desde, $hasta, $limit)
    {
        return DerivacionEntity::with(['medico', 'afiliado', 'medico.traslado', 'medico.movil', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->orderByDesc('id_derivacion')
            ->limit($limit)
            ->get();
    }

    public function findByListBetweenAndDni($desde, $hasta, $dni, $limit)
    {
        return DerivacionEntity::with(['medico', 'afiliado', 'medico.traslado', 'medico.movil', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->where('dni_afiliado', 'LIKE', $dni . '%')
            ->orderByDesc('id_derivacion')
            ->limit($limit)
            ->get();
    }

    public function findByListBetweenAndAfiliado($desde, $hasta, $afiliado, $limit)
    {
        return DerivacionEntity::with(['medico', 'afiliado', 'medico.traslado', 'medico.movil', 'estado'])
            ->whereBetween('fecha_solicitud', [$desde, $hasta])
            ->whereHas('afiliado', function ($query) use ($afiliado) {
                $query->where('apellidos', 'LIKE', $afiliado . '%');
                $query->orWhere('nombre', 'LIKE', $afiliado . '%');
            })
            ->orderByDesc('id_derivacion')
            ->limit($limit)
            ->get();
    }

    public function findByListDni($dni)
    {
        return DerivacionEntity::with(['medico', 'afiliado', 'medico.traslado', 'medico.movil', 'estado'])
            ->where('dni_afiliado',   $dni)
            ->orderByDesc('id_derivacion')
            ->get();
    }
}
