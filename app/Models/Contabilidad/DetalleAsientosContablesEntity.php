<?php

namespace App\Models\Contabilidad;

use App\Models\Prestadores\PrestadorEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAsientosContablesEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_asientos_contables_detalle';
    protected $primaryKey = 'id_asiento_contable_detalle';
    public $timestamps = false;

    protected $fillable = [
        'id_asiento_contable',
        'cod_proveedor',
        'cod_prestador',
        'id_proveedor_cuenta_contable',
        'id_tipo_prestador_cuenta_contable',
        'id_forma_pago_cuenta_contable',
        'id_familia_cuenta_contable',
        'id_cuenta_bancaria_cuenta_contable',
        'id_retencion_cuenta_contable',
        'id_pago_retencion',
        'monto_debe',
        'monto_haber',
        'observaciones',
        'id_detalle_plan',
        'recursor'
    ];

    public function asientoContable()
    {
        return $this->hasOne(AsientosContablesEntity::class, 'id_asiento_contable', 'id_asiento_contable');
    }
    public function proveedor()
    {
        return $this->hasOne(MatrizProveedoresEntity::class, 'cod_proveedor', 'cod_proveedor');
    }
    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'cod_prestador');
    }

    public function planCuenta()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }

    public function proveedorCuentaContable()
    {
        return $this->hasOne(ProveedorCuentaContableEntity::class, 'id_proveedor_cuenta_contable', 'id_proveedor_cuenta_contable');
    }

    public function formaPagoCuentaContable()
    {
        return $this->hasOne(FormasPagoCuentasContableEntity::class, 'id_forma_pago_cuenta_contable', 'id_forma_pago_cuenta_contable');
    }
    public function familiaCuentaContable()
    {
        return $this->hasOne(FamiliaCuentaContableEntity::class, 'id_familia_cuenta_contable', 'id_familia_cuenta_contable');
    }

    public function pagoRetencion()
    {
        return $this->belongsTo(\App\Models\Tesoreria\PagoRetencionesEntity::class, 'id_pago_retencion', 'id_pago_retencion');
    }
}
