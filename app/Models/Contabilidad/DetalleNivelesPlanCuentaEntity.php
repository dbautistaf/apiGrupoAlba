<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleNivelesPlanCuentaEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_planes_cuentas_detalle_niveles';
    protected $primaryKey = 'id_detalle_nivel';
    public $timestamps = false;

    protected $fillable = [
        'id_nivel_padre',
        'id_tipo_nivel_plan_cuenta',
        'id_plan_cuenta',
        'vigente'
    ];

    public function padre()
    {
        return $this->hasOne(NivelesPlanCuentaEntity::class, 'id_tipo_nivel_plan_cuenta', 'id_nivel_padre');
    }

    public function nivel()
    {
        return $this->hasOne(NivelesPlanCuentaEntity::class, 'id_tipo_nivel_plan_cuenta', 'id_tipo_nivel_plan_cuenta');
    }
}
