<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\ImputacionesCuentaContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ImputacionCuentaContableRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now();
    }

    public function findByCrear($params)
    {
        return ImputacionesCuentaContableEntity::create([
            'id_detalle_plan' => $params->id_detalle_plan,
            'imputacion' => $params->descripcion,
            'codigo' => $params->codigo_cuenta,
            'vigente' => $params->vigente ?? true,
            'cod_usuario' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_detalle_plan, $codigo)
    {
        return ImputacionesCuentaContableEntity::where('id_detalle_plan', $id_detalle_plan)
            ->where('codigo', $codigo)
            ->where('vigente', true)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $imputacion = ImputacionesCuentaContableEntity::find($id);
        $imputacion->id_detalle_plan = $params->id_detalle_plan;
        $imputacion->imputacion = $params->imputacion;
        $imputacion->codigo = $params->codigo;
        $imputacion->vigente = $params->vigente ?? $imputacion->vigente;
        $imputacion->cod_usuario_modifica = $this->user->cod_usuario;
        $imputacion->fecha_modifica = $this->fechaActual;
        return $imputacion->update();
    }

    public function findByListar()
    {
        return ImputacionesCuentaContableEntity::with(['detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionImputacion($idDetallePlan, $codigo)
    {
        return ImputacionesCuentaContableEntity::where('id_detalle_plan', $idDetallePlan)
            ->where('codigo', $codigo)
            ->where('vigente', true)
            ->first();
    }

    public function findByListarConFiltros($filtros = [])
    {
        $query = ImputacionesCuentaContableEntity::with(['detallePlan']);

        if (isset($filtros['vigente'])) {
            $query->where('vigente', $filtros['vigente']);
        }

        if (isset($filtros['id_detalle_plan'])) {
            $query->where('id_detalle_plan', $filtros['id_detalle_plan']);
        }

        if (isset($filtros['codigo'])) {
            $query->where('codigo', 'like', '%' . $filtros['codigo'] . '%');
        }

        if (isset($filtros['imputacion'])) {
            $query->where('imputacion', 'like', '%' . $filtros['imputacion'] . '%');
        }

        return $query->get();
    }
}
