<?php

namespace App\Http\Controllers\Contabilidad\Repository;

use App\Http\Controllers\Contabilidad\DTOs\PadronMapaPlanesDTOs;
use App\Models\Contabilidad\DetalleNivelesPlanCuentaEntity;
use App\Models\Contabilidad\DetallePlanCuentasEntity;
use App\Models\Contabilidad\PlanesCuentaEntity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PlanesCuentasRepository
{
    private $user;
    private $fechaActual;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->fechaActual = Carbon::now('America/Argentina/Buenos_Aires');
    }

    public function findByCreate($params)
    {
        return PlanesCuentaEntity::create([
            'id_tipo_plan_cuenta' => $params->id_tipo_plan_cuenta,
            'plan_cuenta' => $params->plan_cuenta,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
            'activo' => $params->activo,
        ]);
    }

    public function findByUpdate($params, $id)
    {
        $plan = PlanesCuentaEntity::find($id);
        $plan->id_tipo_plan_cuenta = $params->id_tipo_plan_cuenta;
        $plan->plan_cuenta = $params->plan_cuenta;
        $plan->cod_usuario_modifica = $this->user->cod_usuario;
        $plan->fecha_modifica = $this->fechaActual;
        $plan->activo = $params->activo;
        $plan->update();
        return $plan;
    }

    public function findByList($params)
    {
        return PlanesCuentaEntity::with(['tipo'])
            ->get();
    }

    public function findById($id)
    {
        return PlanesCuentaEntity::with(['tipo'])
            ->find($id);
    }

    public function findByAgregarNivel($params)
    {
        return DetalleNivelesPlanCuentaEntity::create([
            'id_nivel_padre' => $params->id_nivel_padre,
            'id_tipo_nivel_plan_cuenta' => null,
            'id_plan_cuenta' => $params->id_plan_cuenta,
            'vigente' => '1'
        ]);
    }

    public function findByAgregarSubNivel($params)
    {
        return DetalleNivelesPlanCuentaEntity::create([
            'id_nivel_padre' => $params->id_nivel_padre,
            'id_tipo_nivel_plan_cuenta' => $params->id_tipo_nivel_plan_cuenta,
            'id_plan_cuenta' => $params->id_plan_cuenta,
            'vigente' => '1'
        ]);
    }

    public function findByListDetalleNiveles($idPlan)
    {
        return DetalleNivelesPlanCuentaEntity::with(['padre', 'nivel'])->where('id_plan_cuenta', $idPlan)
            ->orderBy('id_detalle_nivel')
            ->get();
    }

    public function findByDeleteNivel($id)
    {
        $padre = DetalleNivelesPlanCuentaEntity::find($id);
        return $padre->delete();
    }

    public function findByAgregarItemEstructuraPalnCuenta($params)
    {
        return DetallePlanCuentasEntity::create([
            'id_plan_cuenta' => $params->id_plan_cuenta,
            'id_nivel_plan_cuenta' => $params->id_nivel_plan_cuenta,
            'codigo_cuenta' => $params->codigo_cuenta,
            'cuenta' => $params->cuenta,
            'id_nivel_padre' => $params->id_nivel_padre,
            'id_tipo_cuenta' => $params->id_tipo_cuenta,
            'vigente' => '1',
            'grupo' => $params->grupo,
            'subgrupo' => $params->subgrupo,
            'id_detalle_nivel' => $params->id_detalle_nivel,
            'cod_usuario_crea' => $this->user->cod_usuario,
            'fecha_registra' => $this->fechaActual,
        ]);
    }

    public function findByCountMatrizPlanCuenta($idPlanCuenta)
    {
        return DetallePlanCuentasEntity::where('id_plan_cuenta', $idPlanCuenta)->count();
    }

    public function findByListDetallePlanesCuentaPorNivel($idPlanCuenta, $grupo)
    {
        return DetallePlanCuentasEntity::with(['tipo'])
            ->where('id_plan_cuenta', $idPlanCuenta)
            ->where('grupo', $grupo)
            ->orderBy('id_detalle_plan')->get();
    }

    public function findByListDetallePlanesCuentaPorSubNivel($idPlanCuenta, $grupo, $subgrupo)
    {
        return DetallePlanCuentasEntity::with(['tipo'])
            ->where('id_plan_cuenta', $idPlanCuenta)
            ->where('grupo', $grupo)
            ->where('subgrupo', $subgrupo)
            ->orderBy('id_detalle_plan')->get();
    }

    public function findByListDetallePorSubNivel($idPlanCuenta, $id_nivel, $grupo, $subgrupo)
    {
        return DetallePlanCuentasEntity::with(['tipo'])
            ->where('id_plan_cuenta', $idPlanCuenta)
            ->where('id_nivel_plan_cuenta', $id_nivel)
            ->where('grupo', $grupo)
            ->where('subgrupo', $subgrupo)
            ->orderBy('id_detalle_plan')->get();
    }

    public function findByAddItem($params, $grupo, $subgrupo, $icon = null, $labelGrupo, $tipo)
    {
        return new PadronMapaPlanesDTOs(
            $params->codigo_cuenta . ' ' . $params->cuenta,
            true,
            'bx bxs-folder-minus',
            'bx bxs-folder-plus',
            [],
            $icon,
            $params->id_detalle_plan,
            $params->id_plan_cuenta,
            $params->id_nivel_plan_cuenta,
            $params->id_nivel_padre,
            $grupo,
            $subgrupo,
            $params->id_detalle_nivel,
            $params->codigo_cuenta,
            null,
            $labelGrupo,
            $params->cuenta,
            $tipo,
            $params->id_tipo_cuenta
        );
    }

    public function findByObtenerNivelPadre($idNivel, $idPlanCuenta)
    {
        return DetalleNivelesPlanCuentaEntity::where('id_nivel_padre', $idNivel)
            ->where('id_plan_cuenta', $idPlanCuenta)
            ->where('vigente', '1')
            ->first();
    }

    public function findByModificarItemEstructuraPalnCuenta($params, $idDetallePlan)
    {
        $item = DetallePlanCuentasEntity::find($idDetallePlan);
        $item->codigo_cuenta = $params->codigo_cuenta;
        $item->cuenta = $params->cuenta;
        $item->id_tipo_cuenta = $params->id_tipo_cuenta;
        $item->cod_usuario_modifica = $this->user->cod_usuario;
        $item->fecha_modifica = $this->fechaActual;
        return $item->update();
    }

    public function findByExistMultiNivel($idDetallePlan, $idPlanCuenta)
    {
        return DetallePlanCuentasEntity::where('id_plan_cuenta', $idPlanCuenta)
            ->where('vigente', '1')
            ->where('grupo', $idDetallePlan)
            ->exists();
    }

    public function findByEliminarItem($idDetallePlan, $idPlanCuenta)
    {
        return DetallePlanCuentasEntity::where('id_detalle_plan', $idDetallePlan)
            ->where('id_plan_cuenta', $idPlanCuenta)
            ->where('vigente', '1')
            ->delete();
    }

    public function findByDetalleCuentasPlanesPrincipal($idNivel)
    {
        return DetallePlanCuentasEntity::with(['tipo', 'plan'])
            ->where('id_nivel_plan_cuenta', $idNivel)
            ->get();
    }
    public function findByDetalleCuentasPlanesCompleto($search = null)
    {
        $query = DetallePlanCuentasEntity::with(['tipo', 'plan']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('cuenta', 'LIKE', '%' . $search . '%')
                    ->orWhere('codigo_cuenta', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->get();
    }
}
