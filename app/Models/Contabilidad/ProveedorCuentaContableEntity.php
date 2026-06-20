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
        'id_razon',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    public function proveedor()
    {
        return $this->belongsTo(MatrizProveedoresEntity::class, 'id_proveedor', 'cod_proveedor');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
}
