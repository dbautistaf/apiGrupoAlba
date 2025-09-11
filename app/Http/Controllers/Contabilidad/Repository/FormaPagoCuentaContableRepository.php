<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\FormasPagoCuentasContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FormaPagoCuentaContableRepository
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
        return FormasPagoCuentasContableEntity::create([
            'id_forma_pago' => $params->id_forma_pago,
            'id_detalle_plan' => $params->id_detalle_plan,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_forma_pago, $id_detalle_plan)
    {
        return FormasPagoCuentasContableEntity::where('id_forma_pago', $id_forma_pago)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = FormasPagoCuentasContableEntity::find($id);
        $proveedor->id_forma_pago = $params->id_forma_pago;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return FormasPagoCuentasContableEntity::with(['formaPago', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionFormaPago($idFormaPago, $idPeriodo)
    {
        return FormasPagoCuentasContableEntity::where('id_forma_pago', $idFormaPago)
            ->whereHas('detallePlan', function ($query) use ($idPeriodo) {
                $query->where('id_periodo_contable', $idPeriodo);
            })
            ->first();
    }
}
