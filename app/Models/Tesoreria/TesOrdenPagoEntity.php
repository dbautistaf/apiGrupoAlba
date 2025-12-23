<?php

namespace App\Models\Tesoreria;

use App\Models\facturacion\FacturacionDatosEntity;
use App\Models\prestadores\PrestadorEntity;
use App\Models\proveedor\MatrizProveedoresEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesOrdenPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_orden_pago';
    protected $primaryKey = 'id_orden_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_proveedor',
        'id_prestador',
        'monto_orden_pago',
        'id_moneda',
        'fecha_emision',
        'fecha_vencimiento',
        'fecha_confirma_pago',
        'id_estado_orden_pago',
        'monto_anticipado',
        'observaciones',
        'cod_usuario',
        'fecha_genera',
        'id_factura',
        'num_orden_pago',
        'tipo_factura',
        'motivo_rechazo',
        'fecha_rechazo',
        'fecha_probable_pago',
        'pago_emergencia',
        'cuotas'
    ];

    public function estado()
    {
        return $this->hasOne(TesEstadoOrdenPagoEntity::class, 'id_estado_orden_pago', 'id_estado_orden_pago');
    }

    public function proveedor()
    {
        return $this->hasOne(MatrizProveedoresEntity::class, 'cod_proveedor', 'id_proveedor');
    }

    public function prestador()
    {
        return $this->hasOne(PrestadorEntity::class, 'cod_prestador', 'id_prestador');
    }

    public function factura()
    {
        return $this->hasOne(FacturacionDatosEntity::class, 'id_factura', 'id_factura');
    }

    public function pagos()
    {
        return $this->hasMany(TesPagoEntity::class, 'id_orden_pago');
    }

    public function pagoFecha()
    {
        return $this->hasOne(TesPagoEntity::class, 'id_orden_pago');
    }

    public function fechapagos()
    {
        return $this->hasOne(TesPagoEntity::class, 'id_orden_pago','id_orden_pago');
    }
}
