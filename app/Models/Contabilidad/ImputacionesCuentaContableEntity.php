<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImputacionesCuentaContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_imputacion_cuenta_contable';
    protected $primaryKey = 'id_imputacion_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_detalle_plan',
        'imputacion',
        'codigo',
        'vigente',
        'cod_usuario',
        'fecha_modifica',
        'cod_usuario_modifica',
        'fecha_registra'
    ];

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }

}
