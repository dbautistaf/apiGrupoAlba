<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\RetencionCuentasContablesEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class RetencionCuentaContableRepository
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
        return RetencionCuentasContablesEntity::create([
            'id_retencion' => $params->id_retencion,
            'id_detalle_plan' => $params->id_detalle_plan,
            'id_razon' => $params->id_razon ?? null,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_retencion, $id_detalle_plan)
    {
        return RetencionCuentasContablesEntity::where('id_retencion', $id_retencion)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = RetencionCuentasContablesEntity::find($id);
        $proveedor->id_retencion = $params->id_retencion;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->id_razon = $params->id_razon ?? null;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return RetencionCuentasContablesEntity::with(['retencion', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionRetencion($id_retencion, $idRazon = null)
    {
        $query = RetencionCuentasContablesEntity::where('id_retencion', $id_retencion);
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }
}
