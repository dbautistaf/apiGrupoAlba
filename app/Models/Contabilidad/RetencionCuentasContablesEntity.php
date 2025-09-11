<?php

namespace App\Models\Contabilidad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetencionCuentasContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_retenciones_cuenta_contable';
    protected $primaryKey = 'id_retencion_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_retencion',
        'id_detalle_plan',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica'
    ];

    public function retencion()
    {
        return $this->hasOne(TipoRetencionesEntity::class, 'id_retencion', 'id_retencion');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
