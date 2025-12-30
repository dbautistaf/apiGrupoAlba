<?php

namespace App\Models\Tesoreria;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TesPagoEntity extends Model
{
    use HasFactory;

    protected $table = 'tb_tes_pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_orden_pago',
        'id_cuenta_bancaria',
        'fecha_registra',
        'fecha_confirma_pago',
        'anticipo',
        'id_forma_pago',
        'monto_pago',
        'observaciones',
        'id_estado_orden_pago',
        'id_usuario',
        'num_pago',
        'monto_anticipado',
        'motivo_rechazo',
        'monto_opa',
        'recursor',
        'tipo_factura',
        'num_cheque',
        'fecha_rechazo',
        'fecha_probable_pago',
        'pago_emergencia',
        'id_forma_cobro',
        'monto_cobro',
        'fecha_confirma_cobro',
        'cuenta_bancaria',
        'imputacion_contable',
        'banco'
    ];

    public function estado()
    {
        return $this->hasOne(TesEstadoOrdenPagoEntity::class, 'id_estado_orden_pago', 'id_estado_orden_pago');
    }

    public function cuenta()
    {
        return $this->hasOne(TesCuentasBancariasEntity::class, 'id_cuenta_bancaria', 'id_cuenta_bancaria');
    }

    public function formaPago()
    {
        return $this->hasOne(TesTipoFormasPagoEntity::class, 'id_forma_pago', 'id_forma_pago');
    }

    public function opa()
    {
        return $this->hasOne(TesOrdenPagoEntity::class, 'id_orden_pago', 'id_orden_pago');
    }

    public function comprobantes()
    {
        return $this->hasMany(TestDetalleComprobantesPagoEntity::class, 'id_pago', 'id_pago');
    }

    public function pagosParciales()
    {
        return $this->hasMany(TesPagosParciales::class, 'id_pago', 'id_pago');
    }

    public function fechaprobablepagos()
    {
        return $this->hasMany(TesFechaProbablePagoEntity::class, 'id_pago', 'id_pago');
    }
}
