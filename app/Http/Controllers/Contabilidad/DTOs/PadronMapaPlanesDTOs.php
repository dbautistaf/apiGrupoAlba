<?php

namespace App\Http\Controllers\Contabilidad\DTOs;

class PadronMapaPlanesDTOs
{
    public $label;
    public $expanded;
    public $expandedIcon;
    public $collapsedIcon;
    public $children;
    public $icon;
    public $id_detalle_plan;
    public $id_plan_cuenta;
    public $id_nivel_plan_cuenta;
    public $id_nivel_padre;
    public $grupo;
    public $subgrupo;
    public $id_detalle_nivel;
    public $plan_cuenta;
    public $id_periodo_contable;
    public $labelGrupo;
    public $cuenta;
    public $tipo;
    public $id_tipo_cuenta;

    public function __construct($label,  $expanded,  $expandedIcon,  $collapsedIcon,  $children,  $icon,  $id_detalle_plan,  $id_plan_cuenta,  $id_nivel_plan_cuenta,  $id_nivel_padre,  $grupo,  $subgrupo,  $id_detalle_nivel,  $plan_cuenta,  $id_periodo_contable,  $labelGrupo = null,  $cuenta = null, $tipo = null, $id_tipo_cuenta = null)
    {
        $this->label = $label;
        $this->expanded = $expanded;
        $this->expandedIcon = $expandedIcon;
        $this->collapsedIcon = $collapsedIcon;
        $this->children = $children;
        $this->icon = $icon;
        $this->id_detalle_plan = $id_detalle_plan;
        $this->id_plan_cuenta = $id_plan_cuenta;
        $this->id_nivel_plan_cuenta = $id_nivel_plan_cuenta;
        $this->id_nivel_padre = $id_nivel_padre;
        $this->grupo = $grupo;
        $this->subgrupo = $subgrupo;
        $this->id_detalle_nivel = $id_detalle_nivel;
        $this->plan_cuenta = $plan_cuenta;
        $this->id_periodo_contable = $id_periodo_contable;
        $this->labelGrupo = $labelGrupo;
        $this->cuenta = $cuenta;
        $this->tipo = $tipo;
        $this->id_tipo_cuenta = $id_tipo_cuenta;
    }
}
