<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\TipoPrestadorCuentaContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TipoPrestadorPlanesCuentaRepository
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
        return TipoPrestadorCuentaContableEntity::create([
            'cod_tipo_prestador' => $params->cod_tipo_prestador,
            'id_detalle_plan' => $params->id_detalle_plan,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($cod_tipo_prestador, $id_detalle_plan)
    {
        return TipoPrestadorCuentaContableEntity::where('cod_tipo_prestador', $cod_tipo_prestador)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $proveedor = TipoPrestadorCuentaContableEntity::find($id);
        $proveedor->cod_tipo_prestador = $params->cod_tipo_prestador;
        $proveedor->id_detalle_plan = $params->id_detalle_plan;
        $proveedor->cod_usuario_modifica = $this->user->cod_usuario;
        $proveedor->fecha_modifica = $this->fechaActual;
        return $proveedor->update();
    }

    public function findByListar()
    {
        return TipoPrestadorCuentaContableEntity::with(['tipoPrestador', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionProveedor($cod_tipo_prestador, $idPeriodo)
    {
        return TipoPrestadorCuentaContableEntity::where('cod_tipo_prestador', $cod_tipo_prestador)
            ->whereHas('detallePlan', function ($query) use ($idPeriodo) {
                $query->where('id_periodo_contable', $idPeriodo);
            })
            ->first();
    }

    public function findByPlanCuentaPorTipoPrestador($codTipoPrestador)
    {
        return TipoPrestadorCuentaContableEntity::where('cod_tipo_prestador', $codTipoPrestador)
            ->where('vigente', 1)
            ->with([
                'tipoPrestador',
                'detallePlan.plan',
                'detallePlan.tipo'
            ])
            ->first();
    }
}
