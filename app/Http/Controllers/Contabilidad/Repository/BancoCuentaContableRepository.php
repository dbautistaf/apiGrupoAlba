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
            'tipo' => $params->tipo ?? BancoCuentasContableEntity::TIPO_BANCO,
            'id_razon' => $params->id_razon ?? null,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    /**
     * Una cuenta bancaria puede tener DOS relaciones: la de banco/caja y la de eCheq diferido.
     * El duplicado se chequea por `(cuenta_bancaria, tipo)`, que es lo que impide el indice
     * unico real de la tabla — chequearlo por `id_detalle_plan` dejaba pasar el caso y el error
     * salia crudo desde SQL. (2026-09-04)
     */
    public function findByExisteRelacion($id_cuenta_bancaria, $tipo, $idExcluir = null)
    {
        return BancoCuentasContableEntity::where('id_cuenta_bancaria', $id_cuenta_bancaria)
            ->where('tipo', $tipo ?: BancoCuentasContableEntity::TIPO_BANCO)
            ->when($idExcluir, fn($q) => $q->where('id_banco_cuenta_contable', '!=', $idExcluir))
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = BancoCuentasContableEntity::find($id);
        $proveedor->id_cuenta_bancaria = $params->id_cuenta_bancaria;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->tipo = $params->tipo ?? BancoCuentasContableEntity::TIPO_BANCO;
        $proveedor->id_razon = $params->id_razon ?? null;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return BancoCuentasContableEntity::with(['banco', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionBanco($idCuentaBancaria, $idRazon = null, $tipo = null)
    {
        $query = BancoCuentasContableEntity::where('id_cuenta_bancaria', $idCuentaBancaria)
            ->where('tipo', $tipo ?: BancoCuentasContableEntity::TIPO_BANCO);
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }
}
