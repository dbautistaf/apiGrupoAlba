<?php

namespace App\Http\Controllers\Protesis\Repository;

use App\Models\Protesis\ProtesisDetalleEntity;
use App\Models\Protesis\ProtesisEntity;

class ProtesisFiltrosRepository
{
    private $relaciones;
    public function __construct()
    {
        $this->relaciones = [
            'afiliado',
            'estado',
            'detalle',
            'detalle.producto',
            'condicion',
            'tipo',
            'origen',
            'diagnostico',
            'provincia',
            'medico',
            'prestador',
            'efector',
            'detalle.cobertura',
            'detalle.programa',
            'detalle.material'
        ];
    }

    public function findByListFechaSolicitaBetweenAndLimit($desde, $hasta, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }


    public function findByListDetalleProtesis($idProtesis)
    {
        return ProtesisDetalleEntity::with(['producto'])
            ->where('id_protesis', $idProtesis)->get();
    }

    public function findByListFechaSolicitaBetweenAndNumPedido($desde, $hasta, $numPedido, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('id_protesis',  ltrim($numPedido, '0'))
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndNumAutoriza($desde, $hasta, $numAutoriza, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('num_autorizacion', 'LIKE',  $numAutoriza . '%')
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndLocatario($desde, $hasta, $locatario, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('id_locatorio',    $locatario)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndDniAfi($desde, $hasta, $dni, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('dni_afiliado', 'LIKE',   $dni . '%')
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndAfiliado($desde, $hasta, $search, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->whereHas('afiliado', function ($query) use ($search) {
                $query->where('apellidos', 'LIKE', $search . '%');
                $query->orWhere('nombre', 'LIKE', $search . '%');
            })
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndEstado($desde, $hasta, $estado, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('id_estado',    $estado)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndDniAfiAndEstado($desde, $hasta, $dni, $estado, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->where('id_estado',    $estado)
            ->where('dni_afiliado', 'LIKE',   $dni . '%')
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListFechaSolicitaBetweenAndAfiliadoAndEstado($desde, $hasta, $search, $estado, $limit)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->whereBetween('fecha_inicia_prestacion', [$desde, $hasta])
            ->where('id_estado',    $estado)
            ->whereHas('afiliado', function ($query) use ($search) {
                $query->where('apellidos', 'LIKE', $search . '%');
                $query->orWhere('nombre', 'LIKE', $search . '%');
            })
            ->orderByDesc('id_protesis')
            ->limit($limit)->get();
    }

    public function findByListDni($dni)
    {
        return ProtesisEntity::with( $this->relaciones)
            ->where('dni_afiliado',    $dni)
            ->orderByDesc('id_protesis')
            ->get();
    }
}
