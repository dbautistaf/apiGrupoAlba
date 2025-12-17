<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\FamiliaCuentaContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class FamiliaPlanesCuentaRepository
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
        return FamiliaCuentaContableEntity::create([
            'id_tipo_factura' => $params->id_tipo_factura,
            'id_detalle_plan' => $params->id_detalle_plan,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_tipo_factura, $id_detalle_plan)
    {
        return FamiliaCuentaContableEntity::where('id_tipo_factura', $id_tipo_factura)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $familia = FamiliaCuentaContableEntity::find($id);
        $familia->id_tipo_factura = $params->id_tipo_factura;
        $familia->id_detalle_plan = $params->id_detalle_plan;
        $familia->cod_usuario_modifica = $this->user->cod_usuario;
        $familia->fecha_modifica = $this->fechaActual;
        return $familia->update();
    }

    public function findByListar()
    {
        return FamiliaCuentaContableEntity::with(['tipoFactura', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionFamilia($idTipoFactura, $idPeriodo)
    {
        return FamiliaCuentaContableEntity::where('id_tipo_factura', $idTipoFactura)
            ->whereHas('detallePlan', function ($query) use ($idPeriodo) {
                $query->where('id_periodo_contable', $idPeriodo);
            })
            ->first();
    }
}
