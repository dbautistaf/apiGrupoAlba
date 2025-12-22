<?php

namespace App\Models\Contabilidad;

use App\Models\Contabilidad\TipoImpuestoEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImpuestoCuentaContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_impuesto_cuenta_contable';
    protected $primaryKey = 'id_impuesto_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_impuesto',
        'id_detalle_plan',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    public function impuesto()
    {
        return $this->hasOne(TipoImpuestoEntity::class, 'id_impuesto', 'id_impuesto');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
