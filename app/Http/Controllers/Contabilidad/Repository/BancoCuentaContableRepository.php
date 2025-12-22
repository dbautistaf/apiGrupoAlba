<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\BancoCuentasContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class BancoCuentaContableRepository
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
        return BancoCuentasContableEntity::create([
            'id_cuenta_bancaria' => $params->id_cuenta_bancaria,
            'id_detalle_plan' => $params->id_detalle_plan,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_cuenta_bancaria, $id_detalle_plan)
    {
        return BancoCuentasContableEntity::where('id_cuenta_bancaria', $id_cuenta_bancaria)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = BancoCuentasContableEntity::find($id);
        $proveedor->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return BancoCuentasContableEntity::with(['banco', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionFormaPago($idFormaPago, $idPeriodo)
    {
        return BancoCuentasContableEntity::where('id_forma_pago', $idFormaPago)
            ->whereHas('detallePlan', function ($query) use ($idPeriodo) {
                $query->where('id_periodo_contable', $idPeriodo);
            })
            ->first();
    }
}
