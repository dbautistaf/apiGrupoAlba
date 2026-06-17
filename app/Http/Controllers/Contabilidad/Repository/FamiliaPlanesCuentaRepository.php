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
            'id_tipo_familia' => $params->id_familia,
            'id_detalle_plan' => $params->id_detalle_plan,
            'id_razon' => $params->id_razon ?? null,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual
        ]);
    }

    public function findByExisteRelacion($id_familia, $id_detalle_plan)
    {
        return FamiliaCuentaContableEntity::where('id_tipo_familia', $id_familia)
            ->where('id_detalle_plan', $id_detalle_plan)
            ->exists();
    }

    public function findByUpdate($params, $id)
    {
        $familia = FamiliaCuentaContableEntity::find($id);
        $familia->id_tipo_familia = $params->id_familia;
        $familia->id_detalle_plan = $params->id_detalle_plan;
        $familia->id_razon = $params->id_razon ?? null;
        $familia->cod_usuario_modifica = $this->user->cod_usuario;
        $familia->fecha_modifica = $this->fechaActual;
        return $familia->update();
    }

    public function findByListar()
    {
        return FamiliaCuentaContableEntity::with(['tipoFamilia', 'detallePlan'])
            ->get();
    }

    public function findByBuscarRelacionFamilia($idTipoFamilia, $idRazon = null)
    {
        $query = FamiliaCuentaContableEntity::where('id_tipo_familia', $idTipoFamilia);
        if ($idRazon) {
            $query->where('id_razon', $idRazon);
        }
        return $query->first();
    }
}
