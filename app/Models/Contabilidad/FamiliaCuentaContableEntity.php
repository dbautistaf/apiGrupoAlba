<?php

namespace App\Models\Contabilidad;

use App\Models\articulos\ArticuloFamiliaEntity;
use App\Models\facturacion\TipoFacturacionEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamiliaCuentaContableEntity extends Model
{
    use HasFactory;
    protected $table = 'tb_cont_familia_cuenta_contable';
    protected $primaryKey = 'id_familia_cuenta_contable';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_familia',
        'id_detalle_plan',
        'cod_usuario_crea',
        'fecha_registra',
        'cod_usuario_modifica',
        'fecha_modifica',
        'vigente'
    ];

    public function tipoFactura()
    {
        return $this->hasOne(TipoFacturacionEntity::class, 'id_tipo_factura', 'id_tipo_factura');
    }

    public function detallePlan()
    {
        return $this->hasOne(DetallePlanCuentasEntity::class, 'id_detalle_plan', 'id_detalle_plan');
    }
    public function familia()
    {
        return $this->hasOne(ArticuloFamiliaEntity::class, 'id_familia', 'id_tipo_familia');
    }
}
