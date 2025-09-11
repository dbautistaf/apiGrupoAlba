<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\ProveedorCuentaContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProveedorPlanesCuentaRepository
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
        return ProveedorCuentaContableEntity::create([
            'id_proveedor' => $params->id_proveedor,
            'id_detalle_plan' => $params->id_detalle_plan,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_proveedor, $id_detalle_plan)
    {
        return ProveedorCuentaContableEntity::where('id_proveedor', $id_proveedor)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = ProveedorCuentaContableEntity::find($id);
        $proveedor->id_proveedor = $params->id_proveedor;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return ProveedorCuentaContableEntity::with(['proveedor', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionProveedor($idProveedor, $idPeriodo)
    {
        return ProveedorCuentaContableEntity::where('id_proveedor', $idProveedor)
            ->whereHas('detallePlan', function ($query) use ($idPeriodo) {
                $query->where('id_periodo_contable', $idPeriodo);
            })
            ->first();
    }
}
