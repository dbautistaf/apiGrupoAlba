<?php

namespace App\Models\Contabilidad;

use App\Models\Tesoreria\TesTipoFormasPagoEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormasPagoCuentasContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_formas_pago_cuenta_contable';
    protected $primaryKey = 'id_forma_pago_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_forma_pago',
        'id_detalle_plan',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica'
    ];

    public function formaPago()
    {
        return $this->hasOne(TesTipoFormasPagoEntity::class, 'id_forma_pago', 'id_forma_pago');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
