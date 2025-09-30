<?php

namespace   App\Http\Controllers\Internaciones\Repository;

use App\Models\Internaciones\InternacionesEntity;
use App\Models\PrestacionesMedicas\DetallePrestacionesPracticaLaboratorioEntity;
use App\Models\PrestacionesMedicas\PrestacionesPracticaLaboratorioEntity;

class InternacionFiltrosRepository
{
    private $relaciones;
    public function __construct()
    {
        //"profesional", "facturacion",
        $this->relaciones = [
            "prestador",

            "tipoPrestacion",
            "tipoInternacion",
            "tipoHabitacion",
            "afiliado.obrasocial",
            "categoria",
            "especialidad",
            "tipoEgreso",
            "tipoDiagnostico",
            "usuario",
            "estadoPrestacion",
            "internacion"
        ];
    }



    public function findByListDniLikeAndLimit($dni, $limit)
    {
        return InternacionesEntity::with($this->relaciones)
            ->where('dni_afiliado', 'like',  $dni . '%')
            ->orderBy('fecha_internacion', 'desc')
            ->limit($limit)
            ->get();
    }


    public function findByListNombresLikeAndLimit($search, $limit)
    {
        return InternacionesEntity::with($this->relaciones)
            ->whereHas('afiliado', function ($query) use ($search) {
                $query->where('nombre', 'like',  $search . '%');
                $query->orWhere('apellidos', 'like',  $search . '%');
            })
            ->orderBy('fecha_internacion', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findByListEstadoLimit($estado, $limit)
    {
        return InternacionesEntity::with($this->relaciones)
            ->where('cod_tipo_estado',  $estado)
            ->orderBy('fecha_internacion', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findByListNewEstadoLimit($estado, $limit)
    {
        return InternacionesEntity::with($this->relaciones)
            ->where('estado',  $estado)
            ->orderBy('fecha_internacion', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findByListLimit($limit)
    {
        return InternacionesEntity::with($this->relaciones)
            ->orderBy('fecha_internacion', 'desc')
            ->limit($limit)
            ->get();
    }

    public function findById($id)
    {
        return InternacionesEntity::with($this->relaciones)
            ->find($id);
    }

    public function findByListDni($dni)
    {
        return InternacionesEntity::with($this->relaciones)
            ->where('dni_afiliado',    $dni)
            ->get();
    }

    public function finByListaDetallePrestaciones($id)
    {
        return  DetallePrestacionesPracticaLaboratorioEntity::with(["practica"])
            ->whereHas('prestacion', function ($query) use ($id) {
                $query->where('cod_internacion', $id);
            })
            ->get();
    }

    public function findByIdExistsAndEstado($id, $estado)
    {
        return InternacionesEntity::where('cod_tipo_estado', $estado)
            ->where('cod_internacion', $id)
            ->exists();
    }

    public function findByExistAndPrestaciones($id)
    {
        return   PrestacionesPracticaLaboratorioEntity::where('cod_internacion', $id)->exists();
    }
}
