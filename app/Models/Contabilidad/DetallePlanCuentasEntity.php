<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePlanCuentasEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_planes_cuentas_detalle';
    protected $primaryKey = 'id_detalle_plan';
    public $timestamps = false;

    protected $fillable = [
        'id_plan_cuenta',
        'id_nivel_plan_cuenta',
        'codigo_cuenta',
        'cuenta',
        'id_nivel_padre',
        'id_tipo_cuenta',
        'id_periodo_contable',
        'vigente',
        'imputable',
        'grupo',
        'subgrupo',
        'id_detalle_nivel',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica'
    ];
    public function tipo()
    {
        return $this->hasOne(TipoPlanOrganicoCuentaEntity::class, 'id_tipo_cuenta', 'id_tipo_cuenta');
    }

    public function periodo()
    {
        return $this->hasOne(PeriodosContablesEntity::class, 'id_periodo_contable', 'id_periodo_contable');
    }

    public function plan()
    {
        return $this->hasOne(PlanesCuentaEntity::class, 'id_plan_cuenta', 'id_plan_cuenta');
    }

}
