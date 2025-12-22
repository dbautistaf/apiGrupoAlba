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

    protected $fillable = [
        'id_cuenta_bancaria',
        'id_detalle_plan',
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
