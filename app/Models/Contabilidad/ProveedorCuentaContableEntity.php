<?php

namespace App\Models\Contabilidad;

use App\Models\proveedor\MatrizProveedoresEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProveedorCuentaContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_proveedor_cuenta_contable';
    protected $primaryKey = 'id_proveedor_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'id_detalle_plan',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica'
    ];

    public function proveedor()
    {
        return $this->hasOne(MatrizProveedoresEntity::class, 'cod_proveedor', 'id_proveedor');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
