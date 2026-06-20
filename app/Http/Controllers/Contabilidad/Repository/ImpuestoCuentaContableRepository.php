<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Models\Contabilidad\ImpuestoCuentaContableEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ImpuestoCuentaContableRepository
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
        return ImpuestoCuentaContableEntity::create([
            'id_impuesto' => $params->id_impuesto,
            'id_detalle_plan' => $params->id_detalle_plan,
            'id_razon' => $params->id_razon ?? null,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_impuesto, $id_detalle_plan)
    {
        return ImpuestoCuentaContableEntity::where('id_impuesto', $id_impuesto)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $impuesto = ImpuestoCuentaContableEntity::find($id);
        $impuesto->id_impuesto = $params->id_impuesto;
        $impuesto->id_detalle_plan = $params->id_detalle_plan;
        $impuesto->id_razon = $params->id_razon ?? null;
        $impuesto->cod_usuario_modifica = $this->user->cod_usuario;
        $impuesto->fecha_modifica = $this->fechaActual;
        return $impuesto->update();
    }

    public function findByListar()
    {
        return ImpuestoCuentaContableEntity::with(['impuesto', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionImpuesto($idImpuesto, $idRazon = null)
    {
        $query = ImpuestoCuentaContableEntity::where('id_impuesto', $idImpuesto);
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }
}
