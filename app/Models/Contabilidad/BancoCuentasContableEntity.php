<?php

namespace App\Models\Contabilidad;

use App\Models\Tesoreria\TesCuentasBancariasEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BancoCuentasContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_banco_cuenta_contable';
    protected $primaryKey = 'id_banco_cuenta_contable';
    public $timestamps = false;

    /**
     * Discriminador de `tipo`: una misma cuenta bancaria tiene su cuenta de banco y, si emite
     * eCheq, su cuenta puente de pasivo. Siempre filtrar por tipo al resolver.
     */
    const TIPO_BANCO          = 'BANCO';
    const TIPO_ECHEQ_DIFERIDO = 'ECHEQ_DIFERIDO';

    protected $fillable = [
        'id_cuenta_bancaria',
        'id_detalle_plan',
        // Sin esto en el fillable, create() lo descartaba en silencio y todo mapeo nuevo
        // nacia como BANCO. (2026-09-04)
        'tipo',
        'id_razon',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    public function banco()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
